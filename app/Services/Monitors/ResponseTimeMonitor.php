<?php

namespace App\Services\Monitors;

use App\Http\Requests\ExecuteMonitorRequest;
use Illuminate\Support\Facades\Http;

class ResponseTimeMonitor implements MonitorInterface
{
    /**
     * Execute the monitor.
     */
    public function execute(ExecuteMonitorRequest $request): MonitorResponse
    {
        $payload = [
            'data' => [
                'response_time' => null,
            ],
            'message' => '',
            'status' => 'success',
        ];

        try {
            $start = microtime(true);
            Http::timeout(15)->get($request->get('address'));
            $end = microtime(true);

            $payload['data']['response_time'] = (($end - $start) * 1000); // Response time in milliseconds.
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
