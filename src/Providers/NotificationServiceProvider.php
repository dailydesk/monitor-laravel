<?php


namespace DailyDesk\Monitor\Laravel\Providers;


use Illuminate\Notifications\Events\NotificationSending;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\ServiceProvider;
use DailyDesk\Monitor\Laravel\Facades\Monitor;
use Inspector\Models\Segment;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Notifications to inspect.
     *
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
        $this->app['events']->listen(NotificationSending::class, function (NotificationSending $event) {
            if (Monitor::canAddSegments()) {
                $this->segments[
                $event->notification->id
                ] = Monitor::startSegment('notification', get_class($event->notification))
                    ->addContext('data', [
                        'Channel' => $event->channel,
                        'Notifiable' => get_class($event->notifiable),
                    ]);
            }
        });

        $this->app['events']->listen(NotificationSent::class, function (NotificationSent $event) {
            if (\array_key_exists($event->notification->id, $this->segments)) {
                $this->segments[$event->notification->id]
                    ->addContext('Response', $event->response)
                    ->end();
            }
        });
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
}
