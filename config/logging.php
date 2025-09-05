<?php

return [

    'default'  => env('LOG_CHANNEL', 'single'),

    'level'    => env('LOG_LEVEL', 'debug'),

    'path'     => env('LOG_PATH', storageLocation('logs/rivulet.log')),

    'channels' => [

        'single' => [
            'driver' => 'single',
            'path'   => env('LOG_PATH', storageLocation('logs/rivulet.log')),
            'level'  => env('LOG_LEVEL', 'debug'),
        ],

        'daily'  => [
            'driver' => 'daily',
            'path'   => storageLocation('logs/rivulet.log'),
            'level'  => env('LOG_LEVEL', 'debug'),
            'days'   => 14,
        ],

    ],

];
