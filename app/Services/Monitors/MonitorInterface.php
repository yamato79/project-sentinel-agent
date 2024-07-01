<?php

namespace App\Services\Monitors;

use App\Http\Requests\ExecuteMonitorRequest;

interface MonitorInterface
{
    /**
     * Execute the monitor.
     */
    public function execute(ExecuteMonitorRequest $request): MonitorResponse;
}
