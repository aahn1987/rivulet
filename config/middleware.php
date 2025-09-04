<?php

return [
    'global'  => [
        \Rivulet\Middleware\CorsMiddleware::class,
    ],

    'route'   => [
        'auth'       => \Rivulet\Middleware\AuthMiddleware::class,
        'rate.limit' => \Rivulet\Middleware\RateLimitMiddleware::class,
    ],

    'groups'  => [
        'web' => [
            \Rivulet\Middleware\CorsMiddleware::class,
        ],

        'api' => [
            \Rivulet\Middleware\CorsMiddleware::class,
            \Rivulet\Middleware\RateLimitMiddleware::class,
        ],
    ],

    'aliases' => [
        'auth'     => \Rivulet\Middleware\AuthMiddleware::class,
        'cors'     => \Rivulet\Middleware\CorsMiddleware::class,
        'throttle' => \Rivulet\Middleware\RateLimitMiddleware::class,
    ],
];
