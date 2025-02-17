<?php

namespace DailyDesk\Monitor\Laravel\Facades;

use DailyDesk\Monitor\Models\Segment;
use DailyDesk\Monitor\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;
use Throwable;

/**
 * @method static Transaction startTransaction(string $name)
 * @method static Transaction transaction()
 * @method static bool needTransaction()
 * @method static bool hasTransaction()
 * @method static bool canAddSegments()
 * @method static bool isRecording()
 * @method static \DailyDesk\Monitor\Monitor startRecording()
 * @method static \DailyDesk\Monitor\Monitor stopRecording()
 * @method static Segment startSegment(string $type, string $label)
 * @method static Segment addSegment(callable $callback, string $type, string $label, bool $throw = false)
 * @method static Segment report(Throwable $e, bool $handled = false)
 * @method static void flush()
 * @method static bool consoleEnabled()
 * @method static bool shouldRecordCommand(string $command)
 * @method static bool shouldRecordException(Throwable $e)
 * @method static bool shouldRecordRequest(Request $request)
 */
class Monitor extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor()
    {
        return 'monitor';
    }
}
