<?php

return [
    'name'      => env('APP_NAME', 'Rivulet'),
    'env'       => env('APP_ENV', 'production'),
    'debug'     => env('APP_DEBUG', false),
    'url'       => env('APP_URL', 'http://localhost'),
    'timezone'  => env('APP_TIMEZONE', 'UTC'),
    'locale'    => env('APP_LOCALE', 'en'),
    'key'       => env('APP_KEY'),

    'providers' => [
        \Rivulet\Providers\AppServiceProvider::class,
        \Rivulet\Providers\DatabaseServiceProvider::class,
        \Rivulet\Providers\RouteServiceProvider::class,
        \Rivulet\Providers\EventServiceProvider::class,
        \Rivulet\Providers\ViewsServiceProvider::class,
        \Rivulet\Providers\FilesystemServiceProvider::class,
        \Rivulet\Providers\MailServiceProvider::class,
        \Rivulet\Providers\QueueServiceProvider::class,
        \Rivulet\Providers\SessionServiceProvider::class,
        \Rivulet\Providers\CookiesServiceProvider::class,
        \Rivulet\Providers\HttpClientServiceProvider::class,
        \Rivulet\Providers\CacheServiceProvider::class,
        \Rivulet\Providers\NotificationServiceProvider::class,
    ],

    'aliases'   => [
        'App'        => \Rivulet\Rivulet::class,
        'Request'    => \Rivulet\Http\Request::class,
        'Response'   => \Rivulet\Http\Response::class,
        'Route'      => \Rivulet\Routing\Router::class,
        'DB'         => \Rivulet\Database\Connection::class,
        'Model'      => \Rivulet\Model::class,
        'Controller' => \Rivulet\Controller::class,
        'View'       => \Rivulet\Views\View::class,
        'Cache'      => \Rivulet\System\Cache\Cache::class,
        'Log'        => \Rivulet\System\Logging\Logs::class,
        'Mail'       => \Rivulet\Mail\Mailer::class,
        'Queue'      => \Rivulet\Queue\Queue::class,
        'Session'    => \Rivulet\Http\Session\Session::class,
        'Cookie'     => \Rivulet\Http\Cookies\Cookies::class,
    ],
];
