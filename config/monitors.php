<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Monitors
    |--------------------------------------------------------------------------
    |
    | This is where we register various monitor classes.
    |
    */

    'domain-expiration' => \App\Services\Monitors\DomainExpirationMonitor::class,
    'domain-nameservers' => \App\Services\Monitors\DomainNameserversMonitor::class,
    'lighthouse' => \App\Services\Monitors\LighthouseMonitor::class,
    'response-code' => \App\Services\Monitors\ResponseCodeMonitor::class,
    'response-time' => \App\Services\Monitors\ResponseTimeMonitor::class,
    'ssl-expiration' => \App\Services\Monitors\SSLExpirationMonitor::class,
    'ssl-validity' => \App\Services\Monitors\SSLValidityMonitor::class,

];
