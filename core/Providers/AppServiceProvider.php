<?php
namespace Rivulet\Providers;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->bind('request', function () {
            return new \Rivulet\Http\Request();
        });

        $this->bind('response', function () {
            return new \Rivulet\Http\Response();
        });

        $this->bind('router', function () {
            return new \Rivulet\Routing\Router();
        });
    }

    public function boot(): void
    {
        date_default_timezone_set(config('app.timezone', 'UTC'));

        if (config('app.debug')) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        } else {
            error_reporting(0);
            ini_set('display_errors', 0);
        }
    }
}
