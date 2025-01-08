<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Enabling
    |--------------------------------------------------------------------------
    |
    | Setting "false" the package stop sending data to Inspector.
    |
    */

    'enabled' => env('MONITOR_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | API Key
    |--------------------------------------------------------------------------
    |
    | You can find your API key on your Inspector project settings page.
    |
    | This API key points the Inspector notifier to the project in your account
    | which should receive your application's events & exceptions.
    |
    */

    'key' => env('MONITOR_API_KEY', env('MONITOR_INGESTION_KEY')),

    /*
    |--------------------------------------------------------------------------
    | Remote URL
    |--------------------------------------------------------------------------
    |
    | You can set the url of the remote endpoint to send data to.
    |
    */

    'url' => env('MONITOR_URL', 'https://monitor.dailydesk.app'),

    /*
    |--------------------------------------------------------------------------
    | Transport method
    |--------------------------------------------------------------------------
    |
    | This is where you can set the data transport method.
    | Supported options: "sync", "async"
    |
    */

    'transport' => env('MONITOR_TRANSPORT', 'async'),

    /*
    |--------------------------------------------------------------------------
    | Max number of items.
    |--------------------------------------------------------------------------
    |
    | Max number of items to record in a single execution cycle.
    |
    */

    'max_items' => env('MONITOR_MAX_ITEMS', 100),

    /*
    |--------------------------------------------------------------------------
    | Custom transport options
    |--------------------------------------------------------------------------
    |
    | This is where you can set the transport option settings you'd like us to use when
    | communicating with Inspector.
    |
    */

    'options' => [
        // 'proxy' => 'https://55.88.22.11:3128',
        // 'curlPath' => '/opt/bin/curl',
    ],

    /*
    |--------------------------------------------------------------------------
    | Query
    |--------------------------------------------------------------------------
    |
    | Enable this if you'd like us to automatically add all queries executed in the timeline.
    |
    */

    'query' => env('MONITOR_QUERY', true),

    /*
    |--------------------------------------------------------------------------
    | Bindings
    |--------------------------------------------------------------------------
    |
    | Enable this if you'd like us to include the query bindings.
    |
    */

    'bindings' => env('MONITOR_QUERY_BINDINGS', true),

    /*
    |--------------------------------------------------------------------------
    | User
    |--------------------------------------------------------------------------
    |
    | Enable this if you'd like us to set the current user logged in via
    | Laravel's authentication system.
    |
    */

    'user' => env('MONITOR_USER', true),

    /*
    |--------------------------------------------------------------------------
    | Email
    |--------------------------------------------------------------------------
    |
    | Enable this if you'd like us to monitor email sending.
    |
    */

    'email' => env('MONITOR_EMAIL', true),

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    |
    | Enable this if you'd like us to monitor notifications.
    |
    */

    'notifications' => env('MONITOR_NOTIFICATIONS', true),

    /*
    |--------------------------------------------------------------------------
    | View
    |--------------------------------------------------------------------------
    |
    | Enable this if you'd like us to monitor background job processing.
    |
    */

    'views' => env('MONITOR_VIEWS', true),

    /*
    |--------------------------------------------------------------------------
    | Job
    |--------------------------------------------------------------------------
    |
    | Enable this if you'd like us to monitor background job processing.
    |
    */

    'job' => env('MONITOR_JOB', true),

    /*
    |--------------------------------------------------------------------------
    | Job
    |--------------------------------------------------------------------------
    |
    | Enable this if you'd like us to monitor background job processing.
    |
    */

    'redis' => env('MONITOR_REDIS', true),

    /*
    |--------------------------------------------------------------------------
    | Exceptions
    |--------------------------------------------------------------------------
    |
    | Enable this if you'd like us to report unhandled exceptions.
    |
    */

    'unhandled_exceptions' => env('MONITOR_UNHANDLED_EXCEPTIONS', true),

    /*
    |--------------------------------------------------------------------------
    | Http Client monitoring
    |--------------------------------------------------------------------------
    |
    | Enable this if you'd like us to report the http requests done using the Laravel Http Client.
    |
    */

    'http_client' => env('MONITOR_HTTP_CLIENT', true),
    'http_client_body' => env('MONITOR_HTTP_CLIENT_BODY', false),

    /*
    |--------------------------------------------------------------------------
    | Hide sensible data from http requests
    |--------------------------------------------------------------------------
    |
    | List request fields that you want mask from the http payload.
    | You can specify nested fields using the dot notation: "user.password"
    */

    'hidden_parameters' => [
        'password',
        'password_confirmation'
    ],

    /*
    |--------------------------------------------------------------------------
    | Artisan command to ignore
    |--------------------------------------------------------------------------
    |
    | Add at this list all command signature that you don't want monitoring
    | in your Inspector dashboard.
    |
    */

    'ignore_commands' => [
        'storage:link',
        'optimize',
        'optimize:clear',
        'schedule:run',
        'schedule:finish',
        'package:discover',
        'vendor:publish',
        'list',
        'test',
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

    /*
    |--------------------------------------------------------------------------
    | Web request url to ignore
    |--------------------------------------------------------------------------
    |
    | Add at this list the url schemes that you don't want monitoring
    | in your Inspector dashboard. You can also use wildcard expression (*).
    |
    */

    'ignore_url' => [
        'telescope*',
        'vendor/telescope*',
        'horizon*',
        'vendor/horizon*',
        'nova*'
    ],

    /*
    |--------------------------------------------------------------------------
    | Job classes to ignore
    |--------------------------------------------------------------------------
    |
    | Add at this list the job classes that you don't want monitoring
    | in your Inspector dashboard.
    |
    */

    'ignore_jobs' => [
        //\App\Jobs\MyJob::class
    ],
];
