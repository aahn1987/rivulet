<?php

return [
    'lifetime'  => env('COOKIE_LIFETIME', 7200),
    'path'      => env('COOKIE_PATH', '/'),
    'domain'    => env('COOKIE_DOMAIN', null),
    'secure'    => env('COOKIE_SECURE', false),
    'http_only' => env('COOKIE_HTTP_ONLY', true),
    'same_site' => env('COOKIE_SAME_SITE', 'lax'),
    'encrypt'   => env('COOKIE_ENCRYPT', false),

];
