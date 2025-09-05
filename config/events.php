<?php

return [

    'listeners'   => [

        'user.created' => [
            // App\Listeners\SendWelcomeEmail::class,
        ],

        'auth.login'   => [
            // App\Listeners\LogSuccessfulLogin::class,
        ],

    ],

    'subscribers' => [
        // App\Listeners\UserEventSubscriber::class,
    ],

];
