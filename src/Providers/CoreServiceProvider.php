<?php

namespace DailyDesk\Monitor\Laravel\Providers;

use DailyDesk\Monitor\Laravel\Console\MonitorTestCommand;
use DailyDesk\Monitor\Laravel\Monitor;
use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/monitor.php' => $this->app->configPath('monitor.php'),
        ]);

        $this->commands([
            MonitorTestCommand::class,
        ]);
    }

    public function register()
    {
        // Default package configuration
        $this->mergeConfigFrom(__DIR__ . '/../../config/monitor.php', 'monitor');

        // Bind Monitor service class
        $this->app->singleton('monitor', function ($app) {
            $config = $app->make('config');

            $configuration = (new \Inspector\Configuration($config->get('monitor.key')))
                ->setEnabled($config->get('monitor.enabled', true))
                ->setUrl($config->get('monitor.url', 'https://monitor.dailydesk.app'))
                ->setVersion(Monitor::VERSION)
                ->setTransport($config->get('monitor.transport', 'sync'))
                ->setOptions($config->get('monitor.options', []))
                ->setMaxItems($config->get('monitor.max_items', 100));

            return new Monitor($configuration);
        });
    }
}
