<?php

namespace DailyDesk\Monitor\Laravel;

use DailyDesk\Monitor\Configuration;
use DailyDesk\Monitor\Laravel\Console\MonitorTestCommand;
use DailyDesk\Monitor\Laravel\Providers\CommandServiceProvider;
use Illuminate\Support\ServiceProvider;

class MonitorServiceProvider extends ServiceProvider
{
    /**
     * The latest version of the client library.
     *
     * @var string
     */
    const VERSION = 'dev-main';

    /**
     * Booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/monitor.php' => $this->app->configPath('monitor.php'),
        ]);

        $this->commands([
            MonitorTestCommand::class,
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Default package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/monitor.php', 'monitor');

        // Bind Monitor service class
        $this->registerMonitorInstance();

        $this->registerMonitorServiceProviders();
    }

    protected function registerMonitorInstance(): void
    {
        $this->app->singleton('monitor', function ($app) {
            $config = $app->make('config');
            
            $configuration = (new Configuration($config->get('monitor.key')))
                ->setEnabled($config->get('monitor.enabled', true))
                ->setUrl($config->get('monitor.url', 'https://monitor.dailydesk.app'))
                ->setVersion(self::VERSION)
                ->setTransport($config->get('monitor.transport', 'async'))
                ->setOptions($config->get('monitor.options', []))
                ->setMaxItems($config->get('monitor.max_items', 100));

            return new Monitor($configuration);
        });
    }

    /**
     * Bind service providers based on package configuration.
     */
    public function registerMonitorServiceProviders()
    {
        $this->app->register(CommandServiceProvider::class);
    }
}
