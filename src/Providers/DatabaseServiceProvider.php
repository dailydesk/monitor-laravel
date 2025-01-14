<?php

namespace DailyDesk\Monitor\Laravel\Providers;

use DailyDesk\Monitor\Laravel\Facades\Monitor;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * Booting of services.
     *
     * @return void
     */
    public function boot()
    {
        if (! config('monitor.database.enabled')) {
            return;
        }
        $this->app['events']->listen(QueryExecuted::class, function (QueryExecuted $query) {
            if (Monitor::canAddSegments() && $query->sql) {
                $this->handleQueryReport($query->sql, $query->bindings, $query->time, $query->connectionName);
            }
        });
    }

    /**
     * Attach a span to monitor query execution.
     *
     * @param $sql
     * @param array $bindings
     * @param $time
     * @param $connection
     */
    protected function handleQueryReport($sql, array $bindings, $time, $connection)
    {
        $segment = Monitor::startSegment($connection, $sql)
            ->start(\microtime(true) - $time / 1000);

        $context = [
            'connection' => $connection,
            'query' => $sql,
        ];

        if (config('monitor.database.bindings')) {
            $context['bindings'] = $bindings;
        }

        $segment->addContext('db', $context)->end($time);
    }
}
