<?php


namespace DailyDesk\Monitor\Laravel\Providers;


use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\ServiceProvider;
use DailyDesk\Monitor\Laravel\Facades\Monitor;
use DailyDesk\Monitor\Laravel\Filters;

class CommandServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $segments = [];

    /**
     * Booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['events']->listen(CommandStarting::class, function (CommandStarting $event) {
            if (!$this->shouldBeMonitored($event->command)) {
                return;
            }

            if (Monitor::needTransaction()) {
                Monitor::startTransaction($event->command)
                    ->setType('command')
                    ->addContext('Command', [
                        'arguments' => $event->input->getArguments(),
                        'options' => $event->input->getOptions(),
                    ]);
            } elseif (Monitor::canAddSegments()) {
                $this->segments[$event->command] = Monitor::startSegment('artisan', $event->command);
            }
        });

        $this->app['events']->listen(CommandFinished::class, function (CommandFinished $event) {
            if (!$this->shouldBeMonitored($event->command)) {
                return;
            }

            if (Monitor::hasTransaction() && Monitor::transaction()->name === $event->command) {
                Monitor::transaction()->setResult($event->exitCode === 0 ? 'success' : 'error');
            } elseif (\array_key_exists($event->command, $this->segments)) {
                $this->segments[$event->command]->end()->addContext('Command', [
                    'exit_code' => $event->exitCode,
                    'arguments' => $event->input->getArguments(),
                    'options' => $event->input->getOptions(),
                ]);
            }
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Determine if the current command should be monitored.
     *
     * @param null|string $command
     * @return bool
     */
    protected function shouldBeMonitored(?string $command): bool
    {
        if (\is_string($command)) {
            return Filters::isApprovedArtisanCommand($command, config('inspector.ignore_commands'));
        }

        return false;
    }
}
