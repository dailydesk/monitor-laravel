<?php

if (! function_exists('monitor')) {
    function monitor(): \DailyDesk\Monitor\Laravel\Monitor
    {
        return app('monitor');
    }
}
