<?php

namespace DailyDesk\Monitor\Laravel\Providers;

use DailyDesk\Monitor\Laravel\Facades\Monitor;
use DailyDesk\Monitor\Models\Segment;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class GateServiceProvider extends ServiceProvider
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
        if (config('monitor.gate.enabled')) {
            Gate::before([$this, 'beforeGateCheck']);
            Gate::after([$this, 'afterGateCheck']);
        }
    }

    /**
     * Intercepting before gate check.
     */
    public function beforeGateCheck(Authenticatable $user, string $ability, array $arguments): void
    {
        if (Monitor::canAddSegments()) {
            $class = ! empty($arguments)
                ? (is_string($arguments[0]) ? $arguments[0] : '')
                : '';

            $label = "Gate::$ability($class)";

            $this->segments[
            $this->generateUniqueKey($this->formatArguments($arguments))
            ] = Monitor::startSegment('gate', $label)
                ->addContext('user', $user);
        }
    }

    /**
     * Intercepting after gate check.
     */
    public function afterGateCheck(Authenticatable $user, string $ability, bool $result, array $arguments): bool
    {
        $arguments = $this->formatArguments($arguments);
        $key = $this->generateUniqueKey($arguments);

        if (array_key_exists($key, $this->segments)) {
            $this->segments[$key]
                ->addContext('check', [
                    'ability' => $ability,
                    'result' => $result ? 'allowed' : 'denied',
                    'arguments' => $arguments,
                ])
                ->end();

            if ($caller = $this->getCallerFromStackTrace()) {
                $this->segments[$key]
                    ->addContext('caller', [
                        'file' => $caller['file'],
                        'line' => $caller['line'],
                    ]);
            }
        }

        return $result;
    }

    /**
     * Generate a unique key to track segment's state.
     */
    public function generateUniqueKey(array $data): string
    {
        return md5(serialize($data));
    }

    /**
     * Format gate arguments.
     */
    public function formatArguments(array $arguments): array
    {
        return array_map(function ($item) {
            return $item instanceof Model ? $this->formatModel($item) : $item;
        }, $arguments);
    }

    /**
     * Human readable model.
     */
    public function formatModel(Model $model): string
    {
        return get_class($model).':'.$model->getKey();
    }

    protected function getCallerFromStackTrace(): array
    {
        $trace = collect(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS))->forget(0);

        return $trace->first(function ($frame) {
            if (! isset($frame['file'])) {
                return false;
            }

            return ! Str::contains($frame['file'], $this->app->basePath('vendor'));
        });
    }
}
