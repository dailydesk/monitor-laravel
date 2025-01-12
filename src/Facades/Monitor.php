<?php

namespace DailyDesk\Monitor\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Inspector\Models\Error;
use Inspector\Models\Segment;
use Inspector\Models\Transaction;

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
 * @method static mixed addSegment($callback, $type, $label = null, $throw = false)
 * @method static Error reportException(\Throwable $exception, $handled = true)
 * @method static void flush()
 * @method static void beforeFlush(callable $callback)
 * @method static Transaction start()
 * @method static Error report(\Throwable $e, $handled = false)
 * @method static bool shouldRecordRequest(\Illuminate\Http\Request $request)
 * @method static bool shouldRecordCommand(?string $command)
 * @method static bool shouldRecordException(\Throwable $e)
 * @method static bool shouldRecordDatabaseQuery()
 * @method static bool shouldRecordDatabaseBindings()
 * @method static bool shouldRecordHttpClient()
 * @method static bool shouldRecordHttpClientBody()
 * @method static bool shouldRecordMail()
 * @method static bool shouldRecordNotification()
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
