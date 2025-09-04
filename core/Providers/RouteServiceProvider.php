<?php
namespace Rivulet\Providers;

use Rivulet\Routing\Router;

class RouteServiceProvider extends ServiceProvider
{
    protected string $namespace = 'App\\Controllers';

    public function register(): void
    {
        $this->bind('router', function () {
            return new Router();
        });
    }

    public function boot(): void
    {
        $this->loadRoutes();
    }

    protected function loadRoutes(): void
    {
        $routeFiles = config('routes', []);

        foreach ($routeFiles as $file) {
            if (file_exists($file)) {
                require_once $file;
            }
        }
    }
}
