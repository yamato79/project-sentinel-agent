<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExecuteMonitorRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MonitorController extends Controller
{
    /**
     * Execute the requested monitor.
     */
    public function index(ExecuteMonitorRequest $request, string $monitorType = '')
    {
        if (! array_key_exists($monitorType, config('monitors'))) {
            return response()->json([
                'error' => 'Invalid monitor type',
            ], 400);
        }

        $monitor = new (config("monitors.{$monitorType}"));
        $payload = array_merge($monitor->execute($request)->toArray(), [
            'app_location' => config('app.location'),
        ]);

        logger()->info($monitorType, [
            'result' => $payload,
            'raw' => $monitor,
        ]);

        return response()->json($payload);
    }
}
