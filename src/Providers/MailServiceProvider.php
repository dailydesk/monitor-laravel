<?php

namespace DailyDesk\Monitor\Laravel\Providers;

use DailyDesk\Monitor\Laravel\Facades\Monitor;
use DailyDesk\Monitor\Models\Segment;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mime\Email;

class MailServiceProvider extends ServiceProvider
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
        if (config('monitor.mail.enabled')) {
            $this->recordMails();
        }
    }

    protected function recordMails(): void
    {
        $this->app['events']->listen(MessageSending::class, function (MessageSending $event) {
            if (Monitor::canAddSegments()) {
                $this->segments[
                $this->getSegmentKey($event->message)
                ] = Monitor::startSegment('mail', get_class($event->message))
                    // Compatibility with Laravel 5.5
                    ->addContext(
                        'data',
                        property_exists($event, 'data')
                            ? array_intersect_key($event->data, array_flip(['mailer']))
                            : []
                    );
            }
        });

        $this->app['events']->listen(MessageSent::class, function (MessageSent $event) {
            $key = $this->getSegmentKey($event->message);

            if (array_key_exists($key, $this->segments)) {
                $this->segments[$key]->end();
            }
        });
    }

    /**
     * Generate a unique key for each message.
     */
    protected function getSegmentKey(Email $message): string
    {
        return sha1(
            json_encode($message->getTo()).$message->getSubject()
        );
    }
}
