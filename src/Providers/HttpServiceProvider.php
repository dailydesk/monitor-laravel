<?php

namespace DailyDesk\Monitor\Laravel\Providers;

use DailyDesk\Monitor\Laravel\Facades\Monitor;
use DailyDesk\Monitor\Laravel\Http\Middleware\MonitorRequests;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Routing\Route;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class HttpServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (! $this->app->runningInConsole() && config('monitor.http.enabled')) {
            $this->recordRequests();
        }
    }

    protected function recordRequests(): void
    {
        /** @var Kernel $kernel */
        $kernel = $this->app->make(HttpKernel::class);

        $middleware = Arr::prepend(
            $kernel->getGlobalMiddleware(),
            MonitorRequests::class
        );

        $kernel->setGlobalMiddleware($middleware);

        $this->app['events']->listen(RequestHandled::class, function (RequestHandled $event) {
            if ($transaction = Monitor::transaction()) {
                $request = $event->request;
                $response = $event->response;

                $route = $request->route();

                if ($route instanceof Route) {
                    $uri = $request->route()->uri();
                    $transaction->name = $request->method() . ' ' . '/' . trim($uri, '/');
                }

                if (config('monitor.http.user') && ($user = $request->user()) instanceof Authenticatable) {
                    $transaction->withUser($user->getAuthIdentifier());
                }

                $transaction->setResult((string) $response->getStatusCode());
            }
        });
    }
}
