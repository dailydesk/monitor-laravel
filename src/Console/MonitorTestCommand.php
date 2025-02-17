<?php

namespace DailyDesk\Monitor\Laravel\Console;

use DailyDesk\Monitor\Laravel\Facades\Monitor;
use DailyDesk\Monitor\Models\Segment;
use Illuminate\Console\Command;

class MonitorTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test DailyDesk Monitor configuration.';

    public function handle()
    {
        $this->line($this->description);

        // Test proc_open function availability
        try {
            \proc_open("", [], $pipes);
        } catch (\Throwable) {
            $this->warn("❌ proc_open function disabled.");

            return;
        }

        if (! Monitor::canAddSegments()) {
            $this->warn('Monitor is not enabled');

            return;
        }

        // Check Monitor API key
        Monitor::addSegment(function (Segment $segment) {
            \usleep(10 * 1000);

            ! empty(config('monitor.key'))
                ? $this->info('✅ Monitor key installed.')
                : $this->warn('❌ Monitor key not specified. Make sure you specify ' .
                'the MONITOR_INGESTION_KEY in your .env file.');

            $segment->addContext('example payload', ['key' => config('monitor.key')]);
        }, 'test', 'Check Ingestion key');

        // Check Monitor is enabled
        Monitor::addSegment(function (Segment $segment) {
            \usleep(10 * 1000);

            config('monitor.enabled')
                ? $this->info('✅ Monitor is enabled.')
                : $this->warn('❌ Monitor is actually disabled, turn to true the `enable` ' .
                'field of the `monitor` config file.');

            $segment->addContext('example payload', ['enable' => config('monitor.enabled')]);
        }, 'test', 'Check if Monitor is enabled');

        // Check CURL
        Monitor::addSegment(function (Segment $segment) {
            \usleep(10 * 1000);

            function_exists('curl_version')
                ? $this->info('✅ CURL extension is enabled.')
                : $this->warn('❌ CURL is actually disabled so your app could not be able to send data to monitor.');
        }, 'test', 'Check CURL extension');

        // Report a bad query
        Monitor::addSegment(function () {
            \sleep(1);
        }, 'mysql', "SELECT name, (SELECT COUNT(*) FROM orders WHERE user_id = users.id) AS order_count FROM users");

        // Report Exception
        Monitor::report(new \Exception('First Exception detected'));

        // End the transaction
        Monitor::transaction()->markAsSuccess()->end();

        $this->line('Done!');
    }
}
