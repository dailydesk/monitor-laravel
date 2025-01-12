<?php

namespace DailyDesk\Monitor\Laravel;

use Throwable;

class Monitor extends \DailyDesk\Monitor\Monitor
{
    public const VERSION = 'dev-main';

    public function call($callback, array $parameters = [])
    {
        if (\is_string($callback)) {
            $label = $callback;
        } elseif (\is_array($callback)) {
            $label = get_class($callback[0]).'@'.$callback[1];
        } else {
            $label = 'closure';
        }

        return $this->addSegment(function ($segment) use ($callback, $parameters) {
            $segment->addContext('Parameters', $parameters);

            return app()->call($callback, $parameters);
        }, 'method', $label, true);
    }

    /**
     * @return bool
     */
    public function shouldRecordException(Throwable $e): bool
    {
        return $this->isRecording() && config('monitor.recording.exception.enabled');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public function shouldRecordRequest($request): bool
    {
        $ignoredUrls = config('monitor.recording.http.ignored_urls');

        return $this->isRecording() && config('monitor.recording.http.enabled') && Filters::isApprovedRequest($ignoredUrls, $request->decodedPath());
    }

    /**
     * @param string|null $command
     * @return bool
     */
    public function shouldRecordCommand($command): bool
    {
        if ($this->isRecording() && config('monitor.recording.console.enabled') && \is_string($command)) {
            return Filters::isApprovedArtisanCommand($command, config('monitor.recording.console.ignored_commands'));
        }

        return false;
    }

    public function shouldRecordDatabaseQuery(): bool
    {
        return $this->isRecording() && config('monitor.recording.database.query');
    }

    public function shouldRecordDatabaseBindings(): bool
    {
        return $this->isRecording() && config('monitor.recording.database.bindings');
    }

    public function shouldRecordHttpClient(): bool
    {
        return $this->isRecording() &&
            config('monitor.recording.http_client.enabled', true) &&
            \class_exists('\Illuminate\Http\Client\Events\RequestSending') &&
            \class_exists('\Illuminate\Http\Client\Events\ResponseReceived');
    }

    public function shouldRecordHttpClientBody(): bool
    {
        return $this->isRecording() && config('monitor.recording.http_client.body', true);
    }

    public function shouldRecordMail(): bool
    {
        return $this->isRecording() && config('monitor.recording.mail.enabled', true);
    }

    public function shouldRecordNotification(): bool
    {
        return $this->isRecording() && config('monitor.recording.notification.enabled', true);
    }


}
