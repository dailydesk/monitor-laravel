<?php

namespace DailyDesk\Monitor\Laravel\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository;
use Inspector\Models\Segment;

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

    public function handle(Repository $config)
    {
        $this->line($this->description);

        // Test proc_open function availability
        try {
            \proc_open("", [], $pipes);
        } catch (\Throwable $exception) {
            $this->warn("❌ proc_open function disabled.");
            return;
        }

        if (!monitor()->canAddSegments()) {
            $this->warn('Monitor is not enabled');
            return;
        }

        // Check Monitor API key
        monitor()->addSegment(function (Segment $segment) use ($config) {
            \usleep(10 * 1000);

            !empty($config->get('monitor.key'))
                ? $this->info('✅ Monitor key installed.')
                : $this->warn('❌ Monitor key not specified. Make sure you specify ' .
                'the MONITOR_INGESTION_KEY in your .env file.');

            $segment->addContext('example payload', ['key' => $config->get('monitor.key')]);
        }, 'test', 'Check Ingestion key');

        // Check Monitor is enabled
        monitor()->addSegment(function (Segment $segment) use ($config) {
            \usleep(10 * 1000);

            $config->get('monitor.enable')
                ? $this->info('✅ Monitor is enabled.')
                : $this->warn('❌ Monitor is actually disabled, turn to true the `enable` ' .
                'field of the `monitor` config file.');

            $segment->addContext('example payload', ['enable' => $config->get('monitor.enable')]);
        }, 'test', 'Check if Monitor is enabled');

        // Check CURL
        monitor()->addSegment(function (Segment $segment) {
            \usleep(10 * 1000);

            function_exists('curl_version')
                ? $this->info('✅ CURL extension is enabled.')
                : $this->warn('❌ CURL is actually disabled so your app could not be able to send data to monitor.');
        }, 'test', 'Check CURL extension');

        // Report a bad query
        monitor()->addSegment(function () {
            \sleep(1);
        }, 'mysql', "SELECT name, (SELECT COUNT(*) FROM orders WHERE user_id = users.id) AS order_count FROM users");

        // Report Exception
        monitor()->reportException(new \Exception('First Exception detected'));

        // End the transaction
        monitor()->transaction()
            ->setResult('success')
            ->end();

        $this->line('Done!');
    }
}
