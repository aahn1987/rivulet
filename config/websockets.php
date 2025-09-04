<?php

return [
    'default'     => env('WEBSOCKET_DRIVER', 'pusher'),

    'connections' => [
        'pusher'  => [
            'driver'     => 'pusher',
            'app_id'     => env('PUSHER_APP_ID'),
            'app_key'    => env('PUSHER_APP_KEY'),
            'app_secret' => env('PUSHER_APP_SECRET'),
            'cluster'    => env('PUSHER_APP_CLUSTER', 'mt1'),
            'encrypted'  => true,
        ],

        'ratchet' => [
            'driver'     => 'ratchet',
            'host'       => env('WEBSOCKET_HOST', '0.0.0.0'),
            'port'       => env('WEBSOCKET_PORT', 8080),
            'path'       => env('WEBSOCKET_PATH', '/ws'),
            'middleware' => [],
        ],
    ],

    'channels'    => [
        'private'  => [
            'prefix' => 'private-',
            'auth'   => true,
        ],
        'presence' => [
            'prefix' => 'presence-',
            'auth'   => true,
        ],
    ],

    'events'      => [
        'connection'    => true,
        'disconnection' => true,
        'message'       => true,
        'broadcast'     => true,
    ],
];
