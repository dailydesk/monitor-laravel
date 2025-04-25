<?php

namespace DailyDesk\Monitor\Laravel\Facades;

use Closure;
use DailyDesk\Monitor\Models\Segment;
use DailyDesk\Monitor\Models\Transaction;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;
use Throwable;

/**
 * @method static bool isRecording()
 * @method static \DailyDesk\Monitor\Laravel\Monitor stopRecording()
 * @method static \DailyDesk\Monitor\Laravel\Monitor startRecording()
 * @method static bool isFlushOnShutdown()
 * @method static \DailyDesk\Monitor\Laravel\Monitor disableFlushOnShutdown()
 * @method static \DailyDesk\Monitor\Laravel\Monitor enableFlushOnShutdown()
 * @method static \DailyDesk\Monitor\HandlerInterface getHandler()
 * @method static \DailyDesk\Monitor\Laravel\Monitor setHandler(\DailyDesk\Monitor\HandlerInterface|Closure|null $handler)
 * @method static array<int, Segment> getSegments()
 * @method static Transaction|null transaction()
 * @method static bool hasTransaction()
 * @method static bool needTransaction()
 * @method static bool canAddSegments()
 * @method static Transaction startTransaction(string $name)
 * @method static Segment startSegment(string $type, string $label)
 * @method static Segment addSegment(callable $callback, string $type, string $label, bool $throw = false)
 * @method static Segment report(Throwable $e, bool $handled = false)
 * @method static void flush()
 * @method static void clear()
 * @method static bool shouldRecordCommand(string $command)
 * @method static bool shouldRecordException(Throwable $e)
 * @method static bool shouldRecordRequest(Request $request)
 * @method static bool shouldRecordJob(Job $job)
 */
class Monitor extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return 'monitor';
    }
}
