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
     * Determine if the given command should be recorded.
     */
    public function shouldRecordCommand(string $command): bool
    {
        return Filters::isApprovedArtisanCommand($command, config('monitor.console.ignored_commands'));
    }

    /**
     * Determine if the given exception should be recorded.
     */
    public function shouldRecordException(Throwable $e): bool
    {
        return true;
    }

    /**
     * Determine if the given request should be recorded.
     *
     * @param  \Illuminate\Http\Request $request
     */
    public function shouldRecordRequest($request): bool
    {
        $ignoredUrls = config('monitor.http.ignored_urls');

        return config('monitor.http.enabled') && Filters::isApprovedRequest($ignoredUrls, $request->decodedPath());
    }
}
