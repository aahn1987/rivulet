<?php

return [

    'timezone' => env('APP_TIMEZONE', 'UTC'),

    'mutex'    => [
        'driver' => 'file',
        'path'   => storageLocation('framework/schedule-mutex'),
    ],

    'output'   => [
        'path' => storageLocation('logs/schedule.log'),
    ],

];
