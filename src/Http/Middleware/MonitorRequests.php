<?php

namespace DailyDesk\Monitor\Laravel\Http\Middleware;

use Closure;
use DailyDesk\Monitor\Laravel\Facades\Monitor;
use DailyDesk\Monitor\Laravel\Filters;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Http\Request;

readonly class MonitorRequests
{
    public function __construct(private Kernel $kernel)
    {
        //
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (Monitor::shouldRecordRequest($request)) {
            $array = explode('?', $request->decodedPath());
            $uri = array_shift($array);

            $transaction = Monitor::startTransaction(
                $request->method() . ' ' . '/' . trim($uri, '/')
            )->markAsRequest();

            $transaction->addContext(
                'request',
                [
                    'body' => Filters::hideParameters($request->all(), config('monitor.http.hidden_parameters')),
                ]
            );

            $startedAt = $this->kernel->requestStartedAt();

            $transaction->timestamp = (float) $startedAt->format('U.u');
        }

        return $next($request);
    }
}
