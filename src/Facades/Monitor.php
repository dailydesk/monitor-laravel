<?php

namespace DailyDesk\Monitor\Laravel\Facades;

use DailyDesk\Monitor\Models\Segment;
use DailyDesk\Monitor\Models\Transaction;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Transaction startTransaction($name)
 * @method static Transaction transaction()
 * @method static bool needTransaction()
 * @method static bool hasTransaction()
 * @method static bool canAddSegments()
 * @method static bool isRecording()
 * @method static \DailyDesk\Monitor\Monitor startRecording()
 * @method static \DailyDesk\Monitor\Monitor stopRecording()
 * @method static Segment startSegment($type, $label)
 * @method static Segment addSegment($callback, $type, $label, $throw = false)
 * @method static Segment report(\Throwable $e, $handled = false)
 * @method static void flush()
 * @method static bool shouldRecordCommand(string $command)
 * @method static bool shouldRecordException(\Throwable $e)
 * @method static bool shouldRecordRequest(\Illuminate\Http\Request $request)
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
