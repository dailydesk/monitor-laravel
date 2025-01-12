<?php

namespace DailyDesk\Monitor\Laravel;

use Illuminate\Support\AggregateServiceProvider;

class MonitorServiceProvider extends AggregateServiceProvider
{
    protected $providers = [
        Providers\ConsoleServiceProvider::class,
        Providers\CoreServiceProvider::class,
        Providers\ExceptionServiceProvider::class,
        Providers\HttpServiceProvider::class,
    ];
}
