<?php

use DailyDesk\Monitor\Laravel\Facades\Monitor;
use Illuminate\Http\Request;

it('ignore exact matched commands', function () {
    config()->set('monitor.console.ignored_commands', ['debug']);

    $this->assertFalse(Monitor::shouldRecordCommand('debug'));
    $this->assertTrue(Monitor::shouldRecordCommand('debug:one'));
    $this->assertTrue(Monitor::shouldRecordCommand('debug:two'));
    $this->assertTrue(Monitor::shouldRecordCommand('debug:any'));

    $this->assertTrue(Monitor::shouldRecordCommand('inspire'));
});

it('ignore wildcard matched commands', function () {
    config()->set('monitor.console.ignored_commands', ['debug*']);

    $this->assertFalse(Monitor::shouldRecordCommand('debug'));
    $this->assertFalse(Monitor::shouldRecordCommand('debug:one'));
    $this->assertFalse(Monitor::shouldRecordCommand('debug:two'));
    $this->assertFalse(Monitor::shouldRecordCommand('debug:any'));

    $this->assertTrue(Monitor::shouldRecordCommand('inspire'));
});

it('ignore exact matched urls', function () {
    config()->set('monitor.http.ignored_urls', ['debug']);

    $request = Request::create('/debug');
    $this->assertFalse(Monitor::shouldRecordRequest($request));

    $request = Request::create('/debug/one');
    $this->assertTrue(Monitor::shouldRecordRequest($request));

    $request = Request::create('/debug/two');
    $this->assertTrue(Monitor::shouldRecordRequest($request));

    $request = Request::create('/debug/three');
    $this->assertTrue(Monitor::shouldRecordRequest($request));

    $request = Request::create('/inspire');
    $this->assertTrue(Monitor::shouldRecordRequest($request));
});

it('ignore wildcard matched urls', function () {
    config()->set('monitor.http.ignored_urls', ['debug*']);

    $request = Request::create('/debug');
    $this->assertFalse(Monitor::shouldRecordRequest($request));

    $request = Request::create('/debug/one');
    $this->assertFalse(Monitor::shouldRecordRequest($request));

    $request = Request::create('/debug/two');
    $this->assertFalse(Monitor::shouldRecordRequest($request));

    $request = Request::create('/debug/three');
    $this->assertFalse(Monitor::shouldRecordRequest($request));

    $request = Request::create('/inspire');
    $this->assertTrue(Monitor::shouldRecordRequest($request));
});
