<?php

return [
    'default'     => env('QUEUE_CONNECTION', 'database'),

    'connections' => [
        'database' => [
            'driver'      => 'database',
            'table'       => 'jobs',
            'connection'  => 'mysql',
            'retry_after' => 90,
            'max_tries'   => 3,
        ],

        'redis'    => [
            'driver'      => 'redis',
            'connection'  => 'default',
            'queue'       => 'default',
            'retry_after' => 90,
            'block_for'   => null,
            'max_tries'   => 3,
        ],

        'rabbitmq' => [
            'driver'    => 'rabbitmq',
            'host'      => env('RABBITMQ_HOST', '127.0.0.1'),
            'port'      => env('RABBITMQ_PORT', 5672),
            'username'  => env('RABBITMQ_USERNAME', 'guest'),
            'password'  => env('RABBITMQ_PASSWORD', 'guest'),
            'vhost'     => env('RABBITMQ_VHOST', '/'),
            'queue'     => env('RABBITMQ_QUEUE', 'default'),
            'max_tries' => 3,
        ],
    ],

    'failed'      => [
        'driver'     => 'database',
        'table'      => 'failed_jobs',
        'connection' => 'mysql',
    ],

    'supervisor'  => [
        'processes' => 4,
        'sleep'     => 3,
        'timeout'   => 60,
        'tries'     => 3,
    ],
];
