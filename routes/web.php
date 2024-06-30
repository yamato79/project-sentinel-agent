<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::match(['GET', 'POST'], '/api/monitors/{monitorType}', [\App\Http\Controllers\Api\MonitorController::class, 'index'])
    ->middleware(\App\Http\Middleware\AuthMiddleware::class)
    ->name('api.monitors.index');
