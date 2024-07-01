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
                'nameservers' => [],
            ],
            'message' => '',
            'status' => 'success',
        ];

        try {
            $domain = parse_url($request->get('address'), PHP_URL_HOST);
            $nameservers = [];

            exec("dig +short NS {$domain}", $nameservers);

            $payload['data']['nameservers'] = $nameservers;
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
