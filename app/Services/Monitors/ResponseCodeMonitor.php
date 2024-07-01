<?php

namespace App\Services\Monitors;

use App\Http\Requests\ExecuteMonitorRequest;
use Illuminate\Support\Facades\Http;

class ResponseCodeMonitor implements MonitorInterface
{
    /**
     * Execute the monitor.
     */
    public function execute(ExecuteMonitorRequest $request): MonitorResponse
    {
        $payload = [
            'data' => [
                'response_code' => null,
            ],
            'message' => '',
            'status' => 'success',
        ];

        try {
            $response = Http::timeout(10)->get($request->get('address'));
            $responseStatus = $response->status();

            $payload['data']['response_code'] = $response->status();
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
