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
        return config('monitor.recording.exception.enabled');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public function shouldRecordRequest($request): bool
    {
        $ignoredUrls = config('monitor.recording.http.ignored_urls');

        return config('monitor.recording.http.enabled') && Filters::isApprovedRequest($ignoredUrls, $request->decodedPath());
    }

    /**
     * @param string|null $command
     * @return bool
     */
    public function shouldRecordCommand($command): bool
    {
        if (config('monitor.recording.console.enabled') && \is_string($command)) {
            return Filters::isApprovedArtisanCommand($command, config('monitor.recording.console.ignored_commands'));
        }

        return false;
    }
}
