<?php

namespace DailyDesk\Monitor\Laravel\Providers;

use DailyDesk\Monitor\Laravel\Facades\Monitor;
use DailyDesk\Monitor\Models\Segment;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\ServiceProvider;

class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * @var Segment[]
     */
    protected array $segments = [];

    /**
     * Booting of services.
     */
    public function boot(): void
    {
        // TODO: if the application isn't running the console or running unit tests or disable console monitoring.
        if (! $this->app->runningInConsole() || $this->app->runningUnitTests() || ! config('monitor.console.enabled')) {
            return;
        }

        $this->app['events']->listen(CommandStarting::class, function (CommandStarting $event) {
            if (! Monitor::shouldRecordCommand($event->command)) {
                return;
            }

            if (Monitor::needTransaction()) {
                Monitor::startTransaction($event->command)
                    ->setType('command')
                    ->addContext('command', [
                        'arguments' => $event->input->getArguments(),
                        'options' => $event->input->getOptions(),
                    ]);
            } elseif (Monitor::canAddSegments()) {
                $this->segments[$event->command] = Monitor::startSegment('artisan', $event->command);
            }
        });

        $this->app['events']->listen(CommandFinished::class, function (CommandFinished $event) {
            if (! Monitor::shouldRecordCommand($event->command)) {
                return;
            }

            if (Monitor::hasTransaction() && Monitor::transaction()->name === $event->command) {
                Monitor::transaction()->setResult($event->exitCode === 0 ? 'success' : 'error');
            } elseif (\array_key_exists($event->command, $this->segments)) {
                $this->segments[$event->command]->end()->addContext('command', [
                    'exit_code' => $event->exitCode,
                    'arguments' => $event->input->getArguments(),
                    'options' => $event->input->getOptions(),
                ]);
            }
        });
    }
}
