<?php

namespace App\Services\Monitors;

use App\Http\Requests\ExecuteMonitorRequest;

class DomainNameserversMonitor implements MonitorInterface
{
    /**
     * Execute the monitor.
     */
    public function execute(ExecuteMonitorRequest $request): MonitorResponse
    {
        $payload = [
            'data' => [
                'domain' => null,
                'nameservers' => [],
            ],
            'message' => '',
            'status' => 'success',
        ];

        try {
            $host = parse_url($request->get('address'), PHP_URL_HOST);
            $hostParts = explode('.', $host);
            $hostParts = array_reverse($hostParts);
            $domain = "{$hostParts[1]}.{$hostParts[0]}";

            $nameservers = [];

            exec("dig +short NS {$domain}", $nameservers);

            $payload['data']['nameservers'] = $nameservers;
            $payload['data']['domain'] = $domain;
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
