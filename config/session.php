<?php

return [

    'driver'          => env('SESSION_DRIVER', 'file'),

    'lifetime'        => env('SESSION_LIFETIME', 120),

    'expire_on_close' => false,

    'encrypt'         => env('SESSION_ENCRYPT', false),

    'files'           => env('SESSION_FILES_PATH', storageLocation('sessions')),

    'connection'      => env('SESSION_CONNECTION', null),

    'table'           => 'sessions',

    'store'           => env('SESSION_STORE', null),

    'lottery'         => [2, 100],

    'cookie'          => env('SESSION_COOKIE', 'rivulet_session'),

    'name'            => env('SESSION_COOKIE', 'rivulet_session'),

    'path'            => env('COOKIE_PATH', '/'),

    'domain'          => env('COOKIE_DOMAIN', null),

    'secure'          => env('COOKIE_SECURE', false),

    'http_only'       => env('COOKIE_HTTP_ONLY', true),

    'same_site'       => env('COOKIE_SAME_SITE', 'lax'),

    'host'            => env('REDIS_HOST', '127.0.0.1'),

    'port'            => env('REDIS_PORT', 6379),

    'password'        => env('REDIS_PASSWORD', null),

    'db'              => env('REDIS_DB', 0),

    'prefix'          => 'rivulet_session:',

];
