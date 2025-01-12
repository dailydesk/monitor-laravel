<?php

namespace DailyDesk\Monitor\Laravel;

use Illuminate\Support\AggregateServiceProvider;

class MonitorServiceProvider extends AggregateServiceProvider
{
    protected $providers = [
        Providers\ConsoleServiceProvider::class,
        Providers\CoreServiceProvider::class,
        Providers\DatabaseServiceProvider::class,
        Providers\ExceptionServiceProvider::class,
        Providers\GateServiceProvider::class,
        Providers\HttpClientServiceProvider::class,
        Providers\HttpServiceProvider::class,
        Providers\MailServiceProvider::class,
        Providers\NotificationServiceProvider::class,
        Providers\QueueServiceProvider::class,
    ];
}
