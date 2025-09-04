<?php

return [

    'default'     => [
        'channels' => ['database'],
    ],

    'channels'    => [

        'database' => [
            'table' => 'notifications',
        ],

        'mail'     => [
            'driver' => env('MAIL_DRIVER', 'smtp'),
        ],

        'firebase' => [
            'server_key' => env('FIREBASE_SERVER_KEY'),
            'sender_id'  => env('FIREBASE_SENDER_ID'),
            'url'        => 'https://fcm.googleapis.com/fcm/send',
        ],

        'pusher'   => [
            'app_id'     => env('PUSHER_APP_ID'),
            'app_key'    => env('PUSHER_APP_KEY'),
            'app_secret' => env('PUSHER_APP_SECRET'),
            'cluster'    => env('PUSHER_APP_CLUSTER', 'mt1'),
            'url'        => 'https://api-{cluster}.pusher.com/apps/{app_id}/events',
            'encrypted'  => true,
        ],

        'slack'    => [
            'webhook_url' => env('SLACK_WEBHOOK_URL'),
            'channel'     => '#general',
            'username'    => env('APP_NAME', 'Rivulet'),
            'icon_emoji'  => ':information_source:',
        ],

        'sms'      => [
            'driver'       => 'twilio',
            'sid'          => env('TWILIO_SID'),
            'auth_token'   => env('TWILIO_AUTH_TOKEN'),
            'phone_number' => env('TWILIO_PHONE_NUMBER'),
            'url'          => 'https://api.twilio.com/2010-04-01/Accounts/{sid}/Messages.json',
        ],

        'whatsapp' => [
            'driver'          => 'facebook',
            'token'           => env('WHATSAPP_TOKEN'),
            'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
            'url'             => 'https://graph.facebook.com/v18.0/{phone_number_id}/messages',
            'api_version'     => 'v18.0',
        ],

    ],

    'queue'       => [
        'connection'  => env('QUEUE_CONNECTION', 'database'),
        'queue'       => 'notifications',
        'delay'       => 0,
        'retry_after' => 90,
        'max_tries'   => 3,
    ],

    'rate_limits' => [
        'firebase' => [
            'requests' => 1000,
            'per'      => 'hour',
        ],
        'sms'      => [
            'requests' => 100,
            'per'      => 'day',
        ],
        'whatsapp' => [
            'requests' => 1000,
            'per'      => 'day',
        ],
        'slack'    => [
            'requests' => 1,
            'per'      => 'second',
        ],
    ],

    'retry'       => [
        'attempts'            => 3,
        'delay'               => [60, 300, 900],
        'exponential_backoff' => true,

        'channels'            => [
            'firebase' => [
                'attempts' => 5,
                'delay'    => [30, 60, 120, 300, 600],
            ],
            'sms'      => [
                'attempts' => 2,
                'delay'    => [300, 900],
            ],
            'whatsapp' => [
                'attempts' => 3,
                'delay'    => [60, 300, 900],
            ],
        ],
    ],

    'logging'     => [
        'enabled'  => env('APP_DEBUG', false),
        'level'    => env('LOG_LEVEL', 'info'),
        'channels' => [
            'firebase' => 'debug',
            'sms'      => 'info',
            'whatsapp' => 'info',
            'slack'    => 'warning',
            'pusher'   => 'debug',
        ],
    ],

    'templates'   => [
        'slack'    => [
            'color'  => 'good',
            'fields' => [
                'show_timestamp' => true,
                'show_app_name'  => true,
            ],
        ],
        'firebase' => [
            'priority'     => 'high',
            'time_to_live' => 3600,
            'collapse_key' => null,
        ],
        'sms'      => [
            'max_length' => 160,
            'encoding'   => 'UTF-8',
        ],
        'whatsapp' => [
            'type'        => 'text',
            'preview_url' => false,
        ],
    ],

    'webhooks'    => [
        'enabled'          => false,
        'url'              => env('APP_URL') . '/webhooks/notifications',
        'secret'           => env('NOTIFICATION_WEBHOOK_SECRET'),
        'verify_signature' => true,
    ],

    'testing'     => [
        'enabled'                => env('APP_ENV') === 'testing',
        'fake_channels'          => ['firebase', 'sms', 'whatsapp', 'slack', 'pusher'],
        'log_fake_notifications' => true,
    ],

];
