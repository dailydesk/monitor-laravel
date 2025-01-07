<?php

namespace DailyDesk\Monitor\Laravel\Http\Middleware;

use Closure;
use DailyDesk\Monitor\Laravel\Facades\Monitor;
use DailyDesk\Monitor\Laravel\Filters;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Inspector\Models\Transaction;

class MonitoringRequests
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     * @throws \Exception
     */
    public function handle($request, Closure $next)
    {
        if (
            Monitor::needTransaction()
            &&
            Filters::isApprovedRequest(config('monitor.ignore_url'), $request->decodedPath())
            &&
            $this->shouldRecorded($request)
        ) {
            $this->startTransaction($request);
        }

        return $next($request);
    }

    /**
     * Determine if Inspector should monitor current request.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    protected function shouldRecorded($request): bool
    {
        return true;
    }

    /**
     * Start a transaction for the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     */
    protected function startTransaction($request)
    {
        $transaction = Monitor::startTransaction(
            $this->buildTransactionName($request)
        )->markAsRequest();

        $transaction->addContext(
            'Request Body',
            Filters::hideParameters($request->all(), config('monitor.hidden_parameters'))
        );

        if (config('monitor.user')) {
            $this->collectUser($transaction);
        }
    }

    public function collectUser(Transaction $transaction)
    {
        if (Auth::check()) {
            $transaction->withUser(Auth::user()->getAuthIdentifier());
        }
    }

    /**
     * Terminates a request/response cycle.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\Response $response
     */
    public function terminate(Request $request, Response $response): void
    {
        if (Monitor::isRecording() && Monitor::hasTransaction()) {
            Monitor::transaction()
                ->addContext('Response', [
                    'status_code' => $response->getStatusCode(),
                    'version' => $response->getProtocolVersion(),
                    'charset' => $response->getCharset(),
                    'headers' => Filters::hideParameters($response->headers->all(), config('monitor.hidden_parameters')),
                ])
                ->addContext('Response Body', \json_decode($response->getContent(), true))
                ->setResult($response->getStatusCode());
        }
    }

    /**
     * Generate readable name.
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    protected function buildTransactionName($request)
    {
        $route = $request->route();

        if ($route instanceof \Illuminate\Routing\Route) {
            $uri = $request->route()->uri();
        } else {
            $array = \explode('?', $_SERVER["REQUEST_URI"]);
            $uri = \array_shift($array);
        }

        return $request->method() . ' ' . $this->normalizeUri($uri);
    }

    /**
     * Normalize URI string.
     *
     * @param $uri
     * @return string
     */
    protected function normalizeUri($uri)
    {
        return '/' . \trim($uri, '/');
    }
}
