<?php


namespace DailyDesk\Monitor\Laravel\Providers;

use DailyDesk\Monitor\Laravel\Facades\Monitor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Inspector\Models\Segment;

class GateServiceProvider extends ServiceProvider
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
        if (! config('monitor.gate.enabled')) {
            return;
        }

        Gate::before([$this, 'beforeGateCheck']);
        Gate::after([$this, 'afterGateCheck']);
    }

    /**
     * Intercepting before gate check.
     *
     * @param \App\Models\User|\Illuminate\Contracts\Auth\Authenticatable $user
     * @param string $ability
     * @param $arguments
     */
    public function beforeGateCheck($user, $ability, $arguments)
    {
        if (Monitor::canAddSegments()) {
            $class = (\is_array($arguments)&&!empty($arguments))
                ? (\is_string($arguments[0]) ? $arguments[0] : '')
                : '';

            $label = "Gate::{$ability}({$class})";

            $this->segments[
            $this->generateUniqueKey($this->formatArguments($arguments))
            ] = Monitor::startSegment('gate', $label)
                ->addContext('user', $user);
        }
    }

    /**
     * Intercepting after gate check.
     *
     * @param  \App\Models\User|\Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string  $ability
     * @param  bool  $result
     * @param  array  $arguments
     * @return bool
     */
    public function afterGateCheck($user, $ability, $result, $arguments)
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
     *
     * @param array $data
     * @return string
     */
    public function generateUniqueKey(array $data)
    {
        return \md5(\serialize($data));
    }

    /**
     * Format gate arguments.
     *
     * @param array $arguments
     * @return array
     */
    public function formatArguments(array $arguments)
    {
        return \array_map(function ($item) {
            return $item instanceof Model ? $this->formatModel($item) : $item;
        }, $arguments);
    }

    /**
     * Human readable model.
     *
     * @param $model
     * @return string
     */
    public function formatModel($model)
    {
        return \get_class($model).':'.$model->getKey();
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

    protected function getCallerFromStackTrace()
    {
        $trace = collect(\debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS))->forget(0);

        return $trace->first(function ($frame) {
            if (! isset($frame['file'])) {
                return false;
            }

            return ! Str::contains($frame['file'], $this->app->basePath('vendor'));
        });
    }
}
