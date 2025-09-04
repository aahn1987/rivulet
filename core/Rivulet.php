<?php
namespace Rivulet;

use Rivulet\Container\Container;

class Rivulet
{
    private static ?Container $container = null;

    public static function getInstance(): Container
    {
        if (static::$container === null) {
            static::$container = new Container();
            static::bootstrap();
        }

        return static::$container;
    }

    private static function bootstrap(): void
    {
        $container = static::$container;

        // Bind core services
        $container->singleton('app', function () use ($container) {
            return $container;
        });

        $container->singleton('request', function () {
            return new Http\Request();
        });

        $container->singleton('response', function () {
            return new Http\Response();
        });

        $container->singleton('router', function () {
            return new Routing\Router();
        });

        $container->singleton('db', function () {
            $config = config('database.connections.mysql');
            return Database\Connection::get($config, 'mysql');
        });

        $container->singleton('cache', function () {
            return new System\Cache\Cache();
        });

        $container->singleton('session', function () {
            return new Http\Session\Session();
        });

        $container->singleton('log', function () {
            return new System\Logging\Logs();
        });

        $container->singleton('events', function () {
            return new Events\Dispatcher();
        });

        $container->singleton('queue', function () {
            return new Queue\Queue();
        });
    }

    public static function setInstance(Container $container): void
    {
        static::$container = $container;
    }

    public static function __callStatic(string $method, array $parameters)
    {
        return static::getInstance()->$method(...$parameters);
    }
}
