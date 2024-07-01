<?php

namespace App\Services\Monitors;

use App\Http\Requests\ExecuteMonitorRequest;
use Spatie\Lighthouse\Lighthouse;

class LighthouseMonitor implements MonitorInterface
{
    /**
     * Execute the monitor.
     */
    public function execute(ExecuteMonitorRequest $request): MonitorResponse
    {
        $payload = [
            'data' => [
                'scores' => [],
                'audits' => [],
            ],
            'message' => '',
            'status' => 'success',
        ];

        try {
            logger()->info("Running Lighthouse");
            $result = Lighthouse::url($request->get('address'))
                ->onlyAudits([
                    "viewport",
                    "first-contentful-paint",
                    "largest-contentful-paint",
                    "first-meaningful-paint",
                    "speed-index",
                    "final-screenshot",
                    "total-blocking-time",
                    "cumulative-layout-shift",
                    "server-response-time",
                    "interactive",
                    "diagnostics",
                    "network-requests",
                    "main-thread-tasks",
                    "metrics",
                    "resource-summary",
                    "third-party-summary",
                    "third-party-facades",
                    "largest-contentful-paint-element",
                    "lcp-lazy-loaded",
                    "layout-shifts",
                    "long-tasks",
                    "non-composited-animations",
                    "prioritize-lcp-image",
                    "script-treemap-data",
                    "render-blocking-resources",
                    "unminified-javascript",
                    "unused-javascript",
                    "efficient-animated-content",
                    "duplicated-javascript",
                    "legacy-javascript",
                ])
                ->timeoutInSeconds(120)
                ->run();
                
            logger()->info("Finished Lighthouse");

            $payload['data']['scores'] = $result->scores();
            $payload['data']['audits'] = $result->audits();
        } catch (\Exception $e) {
            logger()->info("Lighthouse Issue", [
                'raw' => $e->getMessage(),
            ]);
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
