<?php
namespace Rivulet\Console\Commands;

use Rivulet\Console\Command;
use Rivulet\Routing\Router;

class RoutesListCommand extends Command
{
    protected string $name        = 'routes:list';
    protected string $description = 'List all registered routes';

    public function execute(array $args): void
    {
        $this->info('Registered Routes:');
        $this->line(str_repeat('-', 80));

        $router = new Router();

        // Load routes
        $routeFiles = config('routes', []);
        foreach ($routeFiles as $file) {
            if (file_exists($file)) {
                require_once $file;
            }
        }

        $routes = $router->getRoutes();

        if (empty($routes)) {
            $this->warning('No routes registered');
            return;
        }

        $this->line(sprintf('%-8s %-40s %-30s', 'METHOD', 'URI', 'ACTION'));
        $this->line(str_repeat('-', 80));

        foreach ($routes as $route) {
            $method = $route->getMethod();
            $uri    = $route->getUri();
            $action = $this->formatAction($route->getAction());

            $this->line(sprintf('%-8s %-40s %-30s', $method, $uri, $action));
        }
    }

    private function formatAction($action): string
    {
        if (is_array($action)) {
            return implode('@', $action);
        }

        if (is_string($action) && str_contains($action, '@')) {
            return $action;
        }

        if ($action instanceof \Closure) {
            return 'Closure';
        }

        return 'Unknown';
    }
}
