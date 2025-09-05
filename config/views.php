<?php

return [

    'paths'      => [
        resourceLocation('views'),
    ],

    'compiled'   => env('VIEW_COMPILED_PATH', storageLocation('framework/views')),

    'extensions' => [
        'html',
        'php',
    ],

];
