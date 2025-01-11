<?php

namespace DailyDesk\Monitor\Laravel;

use DailyDesk\Monitor\Configuration;
use DailyDesk\Monitor\Laravel\Console\MonitorTestCommand;
use DailyDesk\Monitor\Laravel\Providers\CommandServiceProvider;
use DailyDesk\Monitor\Laravel\Providers\DatabaseServiceProvider;
use DailyDesk\Monitor\Laravel\Providers\MailServiceProvider;
use Illuminate\Support\AggregateServiceProvider;

class MonitorServiceProvider extends AggregateServiceProvider
{
    /**
     * The latest version of the client library.
     *
     * @var string
     */
    public const VERSION = 'dev-main';

    protected $providers = [
        CommandServiceProvider::class,
        DatabaseServiceProvider::class,
        MailServiceProvider::class,
    ];

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
        parent::register();

        // Default package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/monitor.php', 'monitor');

        // Bind Monitor service class
        $this->registerMonitorInstance();
    }

    protected function registerMonitorInstance(): void
    {
        $this->app->singleton('monitor', function ($app) {
            $config = $app->make('config');
            
            $configuration = (new Configuration($config->get('monitor.key')))
                ->setEnabled($config->get('monitor.enabled', true))
                ->setUrl($config->get('monitor.url', 'https://monitor.dailydesk.app'))
                ->setVersion(self::VERSION)
                ->setTransport($config->get('monitor.transport', 'sync'))
                ->setOptions($config->get('monitor.options', []))
                ->setMaxItems($config->get('monitor.max_items', 100));

            return new Monitor($configuration);
        });
    }
}
