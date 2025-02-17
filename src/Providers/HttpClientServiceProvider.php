<?php

namespace DailyDesk\Monitor\Laravel\Providers;

use DailyDesk\Monitor\Laravel\Facades\Monitor;
use DailyDesk\Monitor\Models\Segment;
use Illuminate\Http\Client\Events\RequestSending;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Http\Client\Request;
use Illuminate\Support\ServiceProvider;

class HttpClientServiceProvider extends ServiceProvider
{
    /**
     * @var Segment[]
     */
    protected array $segments = [];

    /**
     * Booting of services.
     *
     * @return void
     */
    public function boot()
    {
        if (config('monitor.http_client.enabled') &&
            class_exists('\Illuminate\Http\Client\Events\RequestSending') &&
            class_exists('\Illuminate\Http\Client\Events\ResponseReceived')) {
            $this->recordHttpRequests();
        }
    }

    protected function recordHttpRequests(): void
    {
        $this->app['events']->listen(RequestSending::class, function (RequestSending $event) {
            if (Monitor::canAddSegments()) {
                $this->segments[
                $this->getSegmentKey($event->request)
                ] = Monitor::startSegment('http_client', $event->request->url());
            }
        });

        $this->app['events']->listen(ResponseReceived::class, function (ResponseReceived $event) {
            $key = $this->getSegmentKey($event->request);

            $type = 'unknown';
            if ($event->request->isForm()) {
                $type = 'form';
            } elseif ($event->request->isJson()) {
                $type = 'json';
            }

            if (array_key_exists($key, $this->segments)) {
                $this->segments[$key]->end()
                    ->addContext('request', [
                        'method' => $event->request->method(),
                        'url' => $event->request->url(),
                        'type' => $type,
                        'headers' => $event->request->headers(),
                        'data' => $event->request->data(),
                    ])
                    ->addContext('response', \array_merge(
                        [
                            'status' => $event->response->status(),
                            'headers' => $event->response->headers(),
                        ],
                        config('monitor.http_client.body') ? ['body' => $event->response->body()] : []
                    ))
                    ->label = $event->response->status() . ' ' .
                    $event->request->method() . ' ' .
                    $event->request->url();
            }
        });
    }

    /**
     * Generate the key to identify the segment in the segment collection.
     */
    protected function getSegmentKey(Request $request): string
    {
        return sha1($request->body());
    }
}
