<?php

namespace App\Services\Monitors;

use App\Http\Requests\ExecuteMonitorRequest;
use Carbon\Carbon;

class SSLValidityMonitor implements MonitorInterface
{
    /**
     * Execute the monitor.
     */
    public function execute(ExecuteMonitorRequest $request): MonitorResponse
    {
        $payload = [
            'data' => [
                'is_valid' => false,
            ],
            'message' => '',
            'status' => 'success',
        ];

        try {
            $parsedUrl = parse_url($request->get('address'));
            $hostname = $parsedUrl['host'];

            $context = stream_context_create(['ssl' => ['capture_peer_cert' => true]]);
            $client = @stream_socket_client('ssl://'.$hostname.':443', $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context);

            if (! $client) {
                throw new \Exception("Unable to establish SSL connection: $errstr ($errno)");
            }

            $cert = stream_context_get_params($client)['options']['ssl']['peer_certificate'];
            $certInfo = openssl_x509_parse($cert);

            $payload['data']['is_valid'] = Carbon::now()->between(
                Carbon::createFromTimestamp($certInfo['validFrom_time_t']),
                Carbon::createFromTimestamp($certInfo['validTo_time_t'])
            );
        } catch (\Exception $e) {
            $payload['message'] = $e->getMessage();
            $payload['status'] = 'error';
        }

        return new MonitorResponse(
            data: $payload['data'],
            message: $payload['message'],
            status: $payload['status']
        );
    }
}
