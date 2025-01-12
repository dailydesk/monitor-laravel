<?php

namespace DailyDesk\Monitor\Laravel\Providers;

use DailyDesk\Monitor\Laravel\Facades\Monitor;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\ServiceProvider;
use Throwable;

class ExceptionServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $handler = $this->app->make(ExceptionHandler::class);

        $handler->reportable(function (Throwable $e) {
            if (! Monitor::shouldRecordException($e)) {
                return;
            }

            if (Monitor::needTransaction()) {
                Monitor::start();
            }

            Monitor::transaction()->setResult('error');

            Monitor::report($e);
        });
    }
}
