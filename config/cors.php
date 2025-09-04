<?php

return [
    'allowed_origins'      => ['*'],
    'allowed_methods'      => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH'],
    'allowed_headers'      => [
        'Content-Type',
        'Authorization',
        'X-Requested-With',
        'X-API-Token',
        'Accept',
        'Origin',
        'Cache-Control',
        'X-Requested-With',
    ],
    'exposed_headers'      => [],
    'max_age'              => 86400,
    'supports_credentials' => false,
];
