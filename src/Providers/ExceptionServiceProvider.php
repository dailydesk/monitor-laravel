<?php

namespace DailyDesk\Monitor\Laravel\Providers;

use DailyDesk\Monitor\Laravel\Facades\Monitor;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Support\ServiceProvider;
use Throwable;

class ExceptionServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (config('monitor.exception.enabled')) {
            /** @var Handler $handler */
            $handler = $this->app->make(ExceptionHandler::class);

            $handler->reportable(function (Throwable $e) {
                if (Monitor::shouldRecordException($e)) {
                    Monitor::report($e);
                }
            });
        }
    }
}
