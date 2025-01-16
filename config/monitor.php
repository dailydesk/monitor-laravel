<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Monitor Enabled Mode
    |--------------------------------------------------------------------------
    |
    | Determine if monitor clients should sending data to the Monitor service.
    |
    */

    'enabled' => env('MONITOR_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Monitor Ingestion Key
    |--------------------------------------------------------------------------
    |
    | This key is for the Monitor service to detect which application should be
    | picked when recording data.
    |
    | You can find this key on your application settings page.
    |
    */

    'key' => env('MONITOR_KEY'),

    'url' => env('MONITOR_URL'),

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

    'database' => [
        'enabled' => env('MONITOR_DB_QUERY', true),

        'query' => [
            'bindings' => env('MONITOR_DB_BINDINGS', true),
        ],
    ],

    'exception' => [
        'enabled' => env('MONITOR_EXCEPTION', true),
    ],

    'gate' => [
        'enabled' => env('MONITOR_GATE', true),
    ],

    'http_client' => [
        'enabled' => env('MONITOR_HTTP_CLIENT', true),
        'body' => env('MONITOR_HTTP_CLIENT_BODY', true),
    ],

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

    'mail' => [
        'enabled' => env('MONITOR_MAIL', true),
    ],

    'notification' => [
        'enabled' => env('MONITOR_NOTIFICATION', true),
    ],

    'queue' => [

        'enabled' => env('MONITOR_QUEUE', true),

        'ignored_jobs' => [
            // \App\Jobs\IgnoredAction::class,
        ],

    ],
];
