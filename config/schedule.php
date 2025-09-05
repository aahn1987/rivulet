<?php

return [

    'timezone' => env('APP_TIMEZONE', 'UTC'),

    'mutex'    => [
        'driver' => 'file',
        'path'   => storage_path('framework/schedule-mutex'),
    ],

    'output'   => [
        'path' => storage_path('logs/schedule.log'),
    ],

];
