<?php

namespace App\Services\Monitors;

use App\Http\Requests\ExecuteMonitorRequest;
use Carbon\Carbon;

class DomainExpirationMonitor implements MonitorInterface
{
    /**
     * Execute the monitor.
     */
    public function execute(ExecuteMonitorRequest $request): MonitorResponse
    {
        $payload = [
            'data' => [
                'domain' => null,
                'expires_in' => null,
            ],
            'message' => '',
            'status' => 'success',
        ];

        try {
            $domain = parse_url($request->get('address'), PHP_URL_HOST);
            $whois = shell_exec("whois $domain");

            if (preg_match('/Registry Expiry Date: (.*)/', $whois, $matches)) {
                $expirationDate = Carbon::parse(trim($matches[1]));
                $payload['data']['expires_in'] = Carbon::now()->diffInDays($expirationDate, false);
                $payload['data']['domain'] = $domain;
            }
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
