<?php

return [
    'default' => env('FILESYSTEM_DRIVER', 'local'),

    'disks'   => [
        'local'   => [
            'driver' => 'local',
            'root'   => storage_path('app'),
        ],

        'public'  => [
            'driver'     => 'local',
            'root'       => public_path('storage'),
            'url'        => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        'uploads' => [
            'driver'     => 'local',
            'root'       => storage_path('uploads'),
            'url'        => env('APP_URL') . '/storage/uploads',
            'visibility' => 'public',
        ],

        's3'      => [
            'driver'                  => 's3',
            'key'                     => env('AWS_ACCESS_KEY_ID'),
            'secret'                  => env('AWS_SECRET_ACCESS_KEY'),
            'region'                  => env('AWS_DEFAULT_REGION'),
            'bucket'                  => env('AWS_BUCKET'),
            'url'                     => env('AWS_URL'),
            'endpoint'                => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
        ],

        'gcs'     => [
            'driver'       => 'gcs',
            'key_file'     => env('GOOGLE_CLOUD_KEY_FILE'),
            'project_id'   => env('GOOGLE_CLOUD_PROJECT_ID'),
            'bucket'       => env('GOOGLE_CLOUD_STORAGE_BUCKET'),
            'path_prefix'  => env('GOOGLE_CLOUD_STORAGE_PATH_PREFIX', ''),
            'api_endpoint' => env('GOOGLE_CLOUD_STORAGE_API_URI', null),
        ],
    ],

    'links'   => [
        public_path('storage') => storage_path('app/public'),
        public_path('uploads') => storage_path('uploads'),
    ],
];
