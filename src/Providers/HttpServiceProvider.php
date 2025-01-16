<?php

namespace DailyDesk\Monitor\Laravel\Providers;

use DailyDesk\Monitor\Laravel\Facades\Monitor;
use DailyDesk\Monitor\Laravel\Filters;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Support\ServiceProvider;

class HttpServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole() || ! config('monitor.http.enabled')) {
            return;
        }

        $this->app->booted(function ($app) {
            /** @var \Illuminate\Foundation\Http\Kernel $kernel */
            $kernel = $app[HttpKernel::class];

            if ($startedAt = $kernel->requestStartedAt()) {
                $request = $app['request'];

                if (Monitor::shouldRecordRequest($request)) {
                    $array = explode('?', $request->decodedPath());
                    $uri = array_shift($array);

                    $transaction = Monitor::startTransaction(
                        $request->method() . ' ' . '/' . trim($uri, '/')
                    )->markAsRequest();

                    $transaction->addContext(
                        'request_body',
                        Filters::hideParameters($request->all(), config('monitor.http.hidden_parameters'))
                    );

                    $transaction->timestamp = (float) $startedAt->format('U.u');
                }
            }
        });

        $this->app['events']->listen(RequestHandled::class, function (RequestHandled $event) {
            if ($transaction = Monitor::transaction()) {
                $request = $event->request;
                $response = $event->response;

                $route = $request->route();

                if ($route instanceof \Illuminate\Routing\Route) {
                    $uri = $request->route()->uri();
                    $transaction->name = $request->method() . ' ' . '/' . trim($uri, '/');
                }

                if (config('monitor.http.user') && ($user = $request->user()) instanceof Authenticatable) {
                    $transaction->withUser($user->getAuthIdentifier());
                }

                $transaction->setResult($response->getStatusCode());
            }
        });
    }
}
