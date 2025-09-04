<?php

return [
    'default'     => env('DB_CONNECTION', 'mysql'),

    'connections' => [
        'mysql'     => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', '127.0.0.1'),
            'port'      => env('DB_PORT', '3306'),
            'database'  => env('DB_DATABASE', 'rivulet'),
            'username'  => env('DB_USERNAME', 'root'),
            'password'  => env('DB_PASSWORD', ''),
            'charset'   => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
        ],

        'secondary' => [
            'driver'    => env('DB_SECONDARY_CONNECTION', 'mysql'),
            'host'      => env('DB_SECONDARY_HOST', '127.0.0.1'),
            'port'      => env('DB_SECONDARY_PORT', '3306'),
            'database'  => env('DB_SECONDARY_DATABASE', 'rivulet_secondary'),
            'username'  => env('DB_SECONDARY_USERNAME', 'root'),
            'password'  => env('DB_SECONDARY_PASSWORD', ''),
            'charset'   => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
        ],

        'pgsql'     => [
            'driver'   => 'pgsql',
            'host'     => env('DB_HOST', '127.0.0.1'),
            'port'     => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'rivulet'),
            'username' => env('DB_USERNAME', 'postgres'),
            'password' => env('DB_PASSWORD', ''),
            'charset'  => 'utf8',
        ],

        'sqlite'    => [
            'driver'   => 'sqlite',
            'database' => env('DB_DATABASE', basePath('database/database.sqlite')),
        ],
    ],

    'migrations'  => [
        'table' => 'migrations',
        'path'  => basePath('database/Migrations'),
    ],
];
