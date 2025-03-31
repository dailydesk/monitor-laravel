<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Monitor Enabled Mode
    |--------------------------------------------------------------------------
    |
    | Determine if clients should send data to the Monitor platform.
    |
    */

    'enabled' => env('MONITOR_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Monitor Ingestion Key
    |--------------------------------------------------------------------------
    |
    | This key is for the Monitor platform to detect which service should be
    | picked when recording data.
    |
    */

    'key' => env('MONITOR_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Monitor URL
    |--------------------------------------------------------------------------
    |
    | The ingestion base URL of the Monitor platform.
    |
    */

    'url' => env('MONITOR_URL', 'https://ingest.dailydesk.app'),

    /*
    |--------------------------------------------------------------------------
    | Console
    |--------------------------------------------------------------------------
    |
    | Define how it should monitor Console commands.
    |
    */

    'console' => [

        'enabled' => env('MONITOR_CONSOLE', true),

        'ignored_commands' => [
            'storage:link',
            'optimize',
            'optimize:clear',
            'schedule:run',
            'schedule:finish',
            'package:discover',
            'vendor:publish',
            'list',
            'test',
            'make:*',
            'migrate',
            'migrate:rollback',
            'migrate:refresh',
            'migrate:fresh',
            'migrate:reset',
            'migrate:install',
            'db:seed',
            'cache:clear',
            'config:cache',
            'config:clear',
            'route:cache',
            'route:clear',
            'view:cache',
            'view:clear',
            'queue:listen',
            'queue:work',
            'queue:restart',
            'vapor:work',
            'vapor:health-check',
            'horizon',
            'horizon:work',
            'horizon:supervisor',
            'horizon:terminate',
            'horizon:snapshot',
            'nova:publish',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Database
    |--------------------------------------------------------------------------
    |
    | Define how it should monitor the Database component.
    |
    */

    'database' => [

        'enabled' => env('MONITOR_DB_QUERY', true),

        'query' => [
            'bindings' => env('MONITOR_DB_BINDINGS', true),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Exception
    |--------------------------------------------------------------------------
    |
    | Define how it should monitor exceptions.
    |
    */

    'exception' => [

        'enabled' => env('MONITOR_EXCEPTION', true),

    ],

    /*
    |--------------------------------------------------------------------------
    | Gate
    |--------------------------------------------------------------------------
    |
    | Define how it should monitor the Gate component.
    |
    */

    'gate' => [

        'enabled' => env('MONITOR_GATE', true),

    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP Client
    |--------------------------------------------------------------------------
    |
    | Define how it should monitor the HTTP Client component.
    |
    */

    'http_client' => [

        'enabled' => env('MONITOR_HTTP_CLIENT', true),

        'body' => env('MONITOR_HTTP_CLIENT_BODY', true),

    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP
    |--------------------------------------------------------------------------
    |
    | Define how it should monitor HTTP requests.
    |
    */

    'http' => [

        'enabled' => env('MONITOR_REQUEST', true),

        'user' => env('MONITOR_USER', true),

        'ignored_urls' => [
            'telescope*',
            'vendor/telescope*',
            'horizon*',
            'vendor/horizon*',
            'nova*',
        ],

        'hidden_parameters' => [
            'password',
            'password_confirmation',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Mail
    |--------------------------------------------------------------------------
    |
    | Define how it should monitor the Mail component.
    |
    */

    'mail' => [

        'enabled' => env('MONITOR_MAIL', true),

    ],

    /*
    |--------------------------------------------------------------------------
    | Notification
    |--------------------------------------------------------------------------
    |
    | Define how it should monitor the Notification component.
    |
    */

    'notification' => [

        'enabled' => env('MONITOR_NOTIFICATION', true),

    ],

    /*
    |--------------------------------------------------------------------------
    | Queue
    |--------------------------------------------------------------------------
    |
    | Define how it should monitor the Queue component.
    |
    */

    'queue' => [

        'enabled' => env('MONITOR_QUEUE', true),

        'ignored_jobs' => [
            // \App\Jobs\IgnoredAction::class,
        ],

    ],
];
