<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MonitorController extends Controller
{
    const MONITOR_TYPES = [
        'response-code' => 'getResponseCode',
        'response-time' => 'getResponseTime',
        'domain-expiry' => 'getDomainExpiry',
        'domain-ns' => 'getDomainNS',
        'ssl-expiry' => 'getSSLExpiry',
        'ssl-valid' => 'getSSLValid',
    ];

    /**
     * Execute the requested monitor.
     */
    public function index(Request $request, string $monitorType = '')
    {
        if (! array_key_exists($monitorType, self::MONITOR_TYPES)) {
            return response()->json(['error' => 'Invalid monitor type'], 400);
        }

        $payload = [
            'app_location' => config('app.location'),
        ];

        $method = self::MONITOR_TYPES[$monitorType];
        $payload[str_replace('-', '_', $monitorType)] = $this->$method($request);

        logger()->info("{$method}: {$request->get('address')}", [
            'raw' => $payload,
        ]);

        return response()->json($payload);
    }

    /**
     * Get the target URL's response code.
     */
    private function getResponseCode(Request $request)
    {
        $responseStatus = 504;

        try {
            $response = Http::timeout(10)->get($request->get('address'));
            $responseStatus = $response->status();
        } catch (\Exception $e) {
            // Handle exception if needed
        }

        return $responseStatus;
    }

    /**
     * Get the target URL's response time.
     */
    private function getResponseTime(Request $request)
    {
        $responseTime = null;
        $start = microtime(true);

        try {
            Http::timeout(15)->get($request->get('address'));
            $end = microtime(true);
            $responseTime = ($end - $start) * 1000; // Response time in milliseconds
        } catch (\Exception $e) {
            // Handle exception if needed
        }

        return $responseTime;
    }

    /**
     * Get the target URL's domain expiration in days.
     */
    private function getDomainExpiry(Request $request)
    {
        try {
            $domain = parse_url($request->get('address'), PHP_URL_HOST);
            $whois = shell_exec("whois $domain");

            if (preg_match('/Registry Expiry Date: (.*)/', $whois, $matches)) {
                $expirationDate = Carbon::parse(trim($matches[1]));
                $expiresIn = Carbon::now()->diffInDays($expirationDate, false);

                return $expiresIn;
            }
        } catch (\Exception $e) {
            // ...
        }

        return null;
    }

    /**
     * Get the target URL's domain nameservers.
     */
    private function getDomainNS(Request $request)
    {
        try {
            $domain = parse_url($request->get('address'), PHP_URL_HOST);
            $nameservers = [];

            exec("dig +short NS {$domain}", $nameservers);

            return $nameservers;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get the target URL's SSL expiration in days.
     */
    private function getSSLExpiry(Request $request)
    {
        try {
            $parsedUrl = parse_url($request->get('address'));
            $hostname = $parsedUrl['host'];

            $context = stream_context_create(['ssl' => ['capture_peer_cert' => true]]);
            $client = stream_socket_client('ssl://'.$hostname.':443', $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context);
            $cert = stream_context_get_params($client)['options']['ssl']['peer_certificate'];
            $certInfo = openssl_x509_parse($cert);

            $expiresIn = Carbon::createFromTimestamp($certInfo['validTo_time_t'])->diffInDays();

            return $expiresIn;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get the target URL's SSL validity.
     */
    private function getSSLValid(Request $request)
    {
        try {
            $parsedUrl = parse_url($request->get('address'));
            $hostname = $parsedUrl['host'];

            $context = stream_context_create(['ssl' => ['capture_peer_cert' => true]]);
            $client = stream_socket_client('ssl://'.$hostname.':443', $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context);
            $cert = stream_context_get_params($client)['options']['ssl']['peer_certificate'];
            $certInfo = openssl_x509_parse($cert);

            $valid = Carbon::now()->between(Carbon::createFromTimestamp($certInfo['validFrom_time_t']), Carbon::createFromTimestamp($certInfo['validTo_time_t']));

            return $valid;
        } catch (\Exception $e) {
            return null;
        }
    }
}
