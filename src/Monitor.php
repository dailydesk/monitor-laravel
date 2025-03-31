<?php

namespace DailyDesk\Monitor\Laravel;

use Illuminate\Contracts\Queue\Job;
use Illuminate\Http\Request;
use Throwable;

class Monitor extends \DailyDesk\Monitor\Monitor
{
    public const VERSION = '1.x-dev';

    public function call($callback, array $parameters = [])
    {
        if (is_string($callback)) {
            $label = $callback;
        } elseif (is_array($callback)) {
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
     */
    public function shouldRecordRequest(Request $request): bool
    {
        $ignoredUrls = config('monitor.http.ignored_urls');

        return config('monitor.http.enabled') && Filters::isApprovedRequest($ignoredUrls, $request->decodedPath());
    }

    /**
     * Determine if the given job should be recorded.
     */
    public function shouldRecordJob(Job $job): bool
    {
        return Filters::isApprovedJobClass($job->resolveName(), config('monitor.queue.ignored_jobs'));
    }
}
