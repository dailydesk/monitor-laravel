<?php

namespace DailyDesk\Monitor\Laravel\Providers;

use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Foundation\Console\Kernel;
use Illuminate\Support\ServiceProvider;
use DailyDesk\Monitor\Laravel\Facades\Monitor;
use Inspector\Models\Segment;
use Symfony\Component\Console\Input\ArgvInput;

class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * @var Segment[]
     */
    protected $segments = [];

    /**
     * Booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->booted(function ($app) {
            /** @var Kernel $kernel */
            $kernel = $app[ConsoleKernel::class];

            if ($startedAt = $kernel->commandStartedAt()) {
                $command = (new ArgvInput)->getFirstArgument() ?: 'list';

                if (Monitor::shouldRecordCommand($command)) {
                    $transaction = Monitor::startTransaction($command)
                        ->setType('command')
                        ->setResult('success');

                    $transaction->timestamp = (float) $startedAt->format('U.u');
                }
            }
        });

        $this->app['events']->listen(CommandStarting::class, function (CommandStarting $event) {
            if ($transaction = Monitor::transaction()) {
                $transaction->name = $event->command;
                $transaction
                    ->addContext('command', [
                        'arguments' => $event->input->getArguments(),
                        'options' => $event->input->getOptions(),
                    ]);
            }
            
            if (Monitor::canAddSegments()) {
                $this->segments[$event->command] = Monitor::startSegment('artisan', $event->command);
            }
        });

        $this->app['events']->listen(CommandFinished::class, function (CommandFinished $event) {
            if (Monitor::hasTransaction() && Monitor::transaction()->name === $event->command) {
                Monitor::transaction()->setResult($event->exitCode === 0 ? 'success' : 'error');
            } 
            
            if (array_key_exists($event->command, $this->segments)) {
                $this->segments[$event->command]->end()->addContext('command', [
                    'exit_code' => $event->exitCode,
                    'arguments' => $event->input->getArguments(),
                    'options' => $event->input->getOptions(),
                ]);
            }
        });
    }
}
