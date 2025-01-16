<?php

namespace DailyDesk\Monitor\Laravel;

use DailyDesk\Monitor\Laravel\Console\MonitorTestCommand;
use Illuminate\Support\AggregateServiceProvider;

class MonitorServiceProvider extends AggregateServiceProvider
{
    protected $providers = [
        Providers\ConsoleServiceProvider::class,
        Providers\DatabaseServiceProvider::class,
        Providers\ExceptionServiceProvider::class,
        Providers\GateServiceProvider::class,
        Providers\HttpClientServiceProvider::class,
        Providers\HttpServiceProvider::class,
        Providers\MailServiceProvider::class,
        Providers\NotificationServiceProvider::class,
        Providers\QueueServiceProvider::class,
    ];

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/monitor.php', 'monitor');

        $this->app->singleton('monitor', function () {
            $enabled = config('monitor.enabled');
            $key = config('monitor.key');

            if ($enabled && $key) {
                $monitor = Monitor::create($key, ['url' => config('monitor.url')]);
                $monitor->startRecording();
            } else {
                $monitor = new Monitor;
                $monitor->stopRecording();
            }

            return $monitor;
        });

        $this->app->alias('monitor', Monitor::class);

        parent::register();
    }

    /**
     * Booting of services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/monitor.php' => $this->app->configPath('monitor.php'),
        ]);

        $this->commands([
            MonitorTestCommand::class,
        ]);
    }
}
