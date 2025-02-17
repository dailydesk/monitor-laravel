<?php

namespace DailyDesk\Monitor\Laravel\Providers;

use DailyDesk\Monitor\Laravel\Facades\Monitor;
use DailyDesk\Monitor\Laravel\Filters;
use DailyDesk\Monitor\Models\Segment;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobReleasedAfterException;
use Illuminate\Support\ServiceProvider;

class QueueServiceProvider extends ServiceProvider
{
    /**
     * @var array<string, Segment>
     */
    protected array $segments = [];

    /**
     * Booting of services.
     *
     * @return void
     */
    public function boot()
    {
        if (! config('monitor.queue.enabled')) {
            return;
        }

        // This event is never called in Laravel Vapor.
        /*Queue::looping(
            function () {
                $this->app['monitor']->flush();
            }
        );*/

        $this->app['events']->listen(
            JobProcessing::class,
            function (JobProcessing $event) {
                if (config('monitor.enabled') && ! Monitor::isRecording()) {
                    Monitor::startRecording();
                }

                if ($this->shouldBeMonitored($event->job->resolveName())) {
                    $this->handleJobStart($event->job);
                }
            }
        );

        $this->app['events']->listen(
            JobExceptionOccurred::class,
            function (JobExceptionOccurred $event) {
                // An unhandled exception will be reported by the ExceptionServiceProvider in case of a sync execution.
                if (Monitor::canAddSegments() && $event->job->getConnectionName() !== 'sync') {
                    Monitor::report($event->exception);
                }
            }
        );

        $this->app['events']->listen(
            JobProcessed::class,
            function ($event) {
                if ($this->shouldBeMonitored($event->job->resolveName()) && Monitor::isRecording()) {
                    $this->handleJobEnd($event->job);
                }
            }
        );

        if (version_compare(app()->version(), '9.0.0', '>=')) {
            $this->app['events']->listen(
                JobReleasedAfterException::class,
                function (JobReleasedAfterException $event) {
                    if ($this->shouldBeMonitored($event->job->resolveName()) && Monitor::isRecording()) {
                        $this->handleJobEnd($event->job, true);

                        // Laravel throws the current exception after raising the failed events.
                        // So after flushing, we turn off the monitoring to avoid ExceptionServiceProvider will report
                        // the exception again causing a new transaction to start.
                        // We'll restart recording in the JobProcessing event at the start of the job lifecycle
                        if ($event->job->getConnectionName() !== 'sync') {
                            Monitor::stopRecording();
                        }
                    }
                }
            );
        }

        $this->app['events']->listen(
            JobFailed::class,
            function (JobFailed $event) {
                if ($this->shouldBeMonitored($event->job->resolveName()) && Monitor::isRecording()) {
                    // JobExceptionOccurred event is called after JobFailed, so we have to report the exception here.
                    Monitor::report($event->exception, false);

                    $this->handleJobEnd($event->job, true);

                    // Laravel throws the current exception after raising the failed events.
                    // So after flushing, we turn off the monitoring to avoid ExceptionServiceProvider will report
                    // the exception again causing a new transaction to start.
                    // We'll restart recording in the JobProcessing event at the start of the job lifecycle
                    if ($event->job->getConnectionName() !== 'sync') {
                        Monitor::stopRecording();
                    }
                }
            }
        );
    }

    /**
     * Determine the way to monitor the job.
     */
    protected function handleJobStart(Job $job): void
    {
        if (Monitor::needTransaction()) {
            Monitor::startTransaction($job->resolveName())
                ->setType('job')
                ->addContext('payload', $job->payload());
        } elseif (Monitor::canAddSegments()) {
            $this->initializeSegment($job);
        }
    }

    /**
     * Representing a job as a segment.
     */
    protected function initializeSegment(Job $job): void
    {
        $segment = Monitor::startSegment('job', $job->resolveName())
            ->addContext('payload', $job->payload());

        // Save the job under a unique ID
        $this->segments[$this->getJobId($job)] = $segment;
    }

    /**
     * Finalize the monitoring of the job.
     */
    public function handleJobEnd(Job $job, bool $failed = false): void
    {
        $id = $this->getJobId($job);

        if (array_key_exists($id, $this->segments)) {
            $this->segments[$id]->end();
        } elseif ($failed) {
            Monitor::transaction()->markAsFailed();
        } else {
            Monitor::transaction()->markAsSuccess();
        }

        /*
         * Flush immediately if the job is processed in a long-running process.
         *
         * We do not have to flush if the application is using the sync driver.
         * In that case, the package considers the job as a segment.
         */
        if ($job->getConnectionName() !== 'sync') {
            Monitor::flush();
        }
    }

    /**
     * Get the job ID.
     */
    protected static function getJobId(Job $job): string
    {
        if ($jobId = $job->getJobId()) {
            return $jobId;
        }

        return sha1($job->getRawBody());
    }

    /**
     * Determine if the given job needs to be monitored.
     */
    protected function shouldBeMonitored(string $job): bool
    {
        return Filters::isApprovedJobClass($job, config('monitor.queue.ignored_jobs'));
    }
}
