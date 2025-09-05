<?php

return [
    'driver'      => env('MAIL_DRIVER', 'smtp'),

    'host'        => env('MAIL_HOST', 'smtp.mailtrap.io'),
    'port'        => env('MAIL_PORT', 587),
    'from'        => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name'    => env('MAIL_FROM_NAME', 'Rivulet'),
    ],

    'encryption'  => env('MAIL_ENCRYPTION', 'tls'),
    'username'    => env('MAIL_USERNAME'),
    'password'    => env('MAIL_PASSWORD'),

    'sendmail'    => '/usr/sbin/sendmail -bs',

    'markdown'    => [
        'theme' => 'default',
        'paths' => [
            resourceLocation('views/vendor/mail'),
        ],
    ],

    'log_channel' => env('MAIL_LOG_CHANNEL'),

    'mailgun'     => [
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark'    => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses'         => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'sendgrid'    => [
        'api_key' => env('SENDGRID_API_KEY'),
    ],
];
