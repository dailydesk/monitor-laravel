<?php

namespace DailyDesk\Monitor\Laravel\Providers;

use Illuminate\Console\Events\ArtisanStarting;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\ServiceProvider;
use DailyDesk\Monitor\Laravel\Facades\Monitor;

class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * Booting of services.
     */
    public function boot(): void
    {
        if (! ($this->app->runningInConsole() && config('monitor.console.enabled'))) {
            return;
        }

        $this->app['events']->listen(ArtisanStarting::class, function (ArtisanStarting $event) {
            $transaction = Monitor::startTransaction('artisan')->setType('command');

            $transaction->timestamp = (float) $this->app[Kernel::class]->commandStartedAt()->format('U.u');
        });

        $this->app['events']->listen(CommandStarting::class, function (CommandStarting $event) {
            if (!Monitor::shouldRecordCommand($event->command)) {
                Monitor::stopRecording();
                return;
            }

            Monitor::transaction()->name = $event->command;

            Monitor::transaction()
                ->addContext('command', [
                    'arguments' => $event->input->getArguments(),
                    'options' => $event->input->getOptions(),
                ]);
        });

        $this->app['events']->listen(CommandFinished::class, function (CommandFinished $event) {
            if (!Monitor::shouldRecordCommand($event->command)) {
                Monitor::stopRecording();
                return;
            }

            Monitor::transaction()
                ->addContext('command', [
                    'exit_code' => $event->exitCode,
                    'arguments' => $event->input->getArguments(),
                    'options' => $event->input->getOptions(),
                ]);

            Monitor::transaction()->setResult($event->exitCode === 0 ? 'success' : 'error');
        });
    }
}
