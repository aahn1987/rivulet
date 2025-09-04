<?php
namespace Rivulet\Console\Commands;

use Rivulet\Console\Command;
use Rivulet\Routing\Router;

class RoutesCacheCommand extends Command
{
    protected string $name        = 'routes:cache';
    protected string $description = 'Cache the route definitions';

    public function execute(array $args): void
    {
        $this->info('Caching routes…');

        $cacheDir = storage_path('framework/cache');
        if (! is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        // Boot router exactly like RoutesListCommand does
        $router = new Router();
        foreach (config('routes', []) as $file) {
            if (file_exists($file)) {
                require_once $file;
            }

        }

        $payload = serialize($router->getRoutes());
        file_put_contents($cacheDir . '/routes.cache', $payload);

        $this->success('Routes cached successfully.');
    }
}
