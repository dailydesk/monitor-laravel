<?php

namespace DailyDesk\Monitor\Laravel\Providers;

use DailyDesk\Monitor\Laravel\Facades\Monitor;
use DailyDesk\Monitor\Models\Segment;
use Illuminate\Notifications\Events\NotificationSending;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
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
        if (config('monitor.notification.enabled')) {
            $this->recordNotifications();
        }
    }

    protected function recordNotifications(): void
    {
        $this->app['events']->listen(NotificationSending::class, function (NotificationSending $event) {
            if (Monitor::canAddSegments()) {
                $this->segments[$event->notification->id] = Monitor::startSegment('notification', get_class($event->notification))
                    ->addContext('data', [
                        'channel' => $event->channel,
                        'notifiable' => get_class($event->notifiable),
                    ]);
            }
        });

        $this->app['events']->listen(NotificationSent::class, function (NotificationSent $event) {
            if (array_key_exists($event->notification->id, $this->segments)) {
                $this->segments[$event->notification->id]
                    ->addContext('response', $event->response)
                    ->end();
            }
        });
    }
}
