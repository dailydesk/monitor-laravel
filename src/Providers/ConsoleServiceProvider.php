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
     * @var array<string, Segment>
     */
    protected array $segments = [];

    /**
     * Booting of services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole() && config('monitor.console.enabled')) {
            $this->recordCommands();
        }
    }

    protected function recordCommands(): void
    {
        $this->app['events']->listen(CommandStarting::class, function (CommandStarting $event) {
            if (! Monitor::shouldRecordCommand($event->command)) {
                return;
            }

            if (Monitor::needTransaction()) {
                Monitor::startTransaction($event->command)
                    ->markAsCommand()
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
                if ($event->exitCode === 0) {
                    Monitor::transaction()->markAsSuccess();
                } else {
                    Monitor::transaction()->markAsFailed();
                }
            } elseif (array_key_exists($event->command, $this->segments)) {
                $this->segments[$event->command]->end()->addContext('command', [
                    'exit_code' => $event->exitCode,
                    'arguments' => $event->input->getArguments(),
                    'options' => $event->input->getOptions(),
                ]);
            }
        });
    }
}
