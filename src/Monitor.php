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

    public function shouldRecordCommand(?string $command): bool
    {
        if (is_string($command)) {
            return Filters::isApprovedArtisanCommand($command, config('monitor.console.ignored_commands'));
        }

        return false;
    }

    public function shouldRecordException(Throwable $e): bool
    {
        return true;
    }

    public function shouldRecordRequest($request): bool
    {
        $ignoredUrls = config('monitor.http.ignored_urls');

        return config('monitor.http.enabled') && Filters::isApprovedRequest($ignoredUrls, $request->decodedPath());
    }
}
