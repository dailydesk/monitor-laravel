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

    'key' => env('MONITOR_API_KEY', env('MONITOR_INGESTION_KEY')),

    /*
    |--------------------------------------------------------------------------
    | Monitor URL
    |--------------------------------------------------------------------------
    */

    'url' => env('MONITOR_URL', 'https://monitor.dailydesk.app'),

    /*
    |--------------------------------------------------------------------------
    | Monitor Transport Method
    |--------------------------------------------------------------------------
    |
    | Supported: "async" or "sync"
    |
    */

    'transport' => env('MONITOR_TRANSPORT', 'async'),

    /*
    |--------------------------------------------------------------------------
    | Monitor Max Items.
    |--------------------------------------------------------------------------
    |
    | Max number of items to record in a single execution cycle.
    |
    */

    'max_items' => env('MONITOR_MAX_ITEMS', 100),

    /*
    |--------------------------------------------------------------------------
    | Monitor Transport Options
    |--------------------------------------------------------------------------
    |
    | Use these options to customize the way monitor clients communicate with
    | the Monitor service
    |
    */

    'options' => [
        // 'proxy' => 'https://55.88.22.11:3128',
        // 'curlPath' => '/opt/bin/curl',
    ],

     'recording' => [

         'console' => [

             'enabled' => env('MONITOR_COMMAND', true),

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

         'exception' => [
             'enabled' => env('MONITOR_EXCEPTION', true)
         ],

         'http' => [

             'enabled' => env('MONITOR_REQUEST', true),

             'user' => env('MONITOR_USER', true),

             'ignored_urls' => [
                 'telescope*',
                 'vendor/telescope*',
                 'horizon*',
                 'vendor/horizon*',
                 'nova*'
             ],

         ],

     ],

    // TODO: Remove the following lines.

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
    | Mail
    |--------------------------------------------------------------------------
    |
    | Enable this if you'd like us to monitor email sending.
    |
    */

    'mail' => env('MONITOR_MAIL', true),

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    |
    | Enable this if you'd like us to monitor notifications.
    |
    */

    'notification' => env('MONITOR_NOTIFICATION', true),

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
    | Job classes to ignore
    |--------------------------------------------------------------------------
    |
    | Add at this list the job classes that you don't want monitoring
    | in your Inspector dashboard.
    |
    */

    'ignored_jobs' => [
        //\App\Jobs\MyJob::class
    ],
];
