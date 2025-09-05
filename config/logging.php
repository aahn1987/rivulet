<?php

return [

    'default'  => env('LOG_CHANNEL', 'single'),

    'level'    => env('LOG_LEVEL', 'debug'),

    'path'     => env('LOG_PATH', storage_path('logs/rivulet.log')),

    'channels' => [

        'single' => [
            'driver' => 'single',
            'path'   => env('LOG_PATH', storage_path('logs/rivulet.log')),
            'level'  => env('LOG_LEVEL', 'debug'),
        ],

        'daily'  => [
            'driver' => 'daily',
            'path'   => storage_path('logs/rivulet.log'),
            'level'  => env('LOG_LEVEL', 'debug'),
            'days'   => 14,
        ],

    ],

];
