<?php
namespace Rivulet\Routing;

use Rivulet\Http\Request;
use Rivulet\Http\Response;

class Router
{
    private array $routes       = [];
    private array $currentGroup = [];
    private ?string $version    = null;

    public function get(string $uri, $action): Route
    {
        return $this->addRoute('GET', $uri, $action);
    }

    public function post(string $uri, $action): Route
    {
        return $this->addRoute('POST', $uri, $action);
    }

    public function put(string $uri, $action): Route
    {
        return $this->addRoute('PUT', $uri, $action);
    }

    public function patch(string $uri, $action): Route
    {
        return $this->addRoute('PATCH', $uri, $action);
    }

    public function delete(string $uri, $action): Route
    {
        return $this->addRoute('DELETE', $uri, $action);
    }

    public function options(string $uri, $action): Route
    {
        return $this->addRoute('OPTIONS', $uri, $action);
    }

    public function any(string $uri, $action): Route
    {
        $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

        foreach ($methods as $method) {
            $this->addRoute($method, $uri, $action);
        }

        return $this->routes[array_key_last($this->routes)];
    }

    public function match(array $methods, string $uri, $action): Route
    {
        foreach ($methods as $method) {
            $this->addRoute($method, $uri, $action);
        }

        return $this->routes[array_key_last($this->routes)];
    }

    private function addRoute(string $method, string $uri, $action): Route
    {
        $uri = $this->applyPrefix($uri);

        $route = new Route($method, $uri, $action);

        if (! empty($this->currentGroup['middleware'])) {
            $route->middleware($this->currentGroup['middleware']);
        }

        if ($this->version) {
            $route->where('version', $this->version);
        }

        $this->routes[] = $route;

        return $route;
    }

    private function applyPrefix(string $uri): string
    {
        $prefix = $this->currentGroup['prefix'] ?? '';

        if ($prefix) {
            $uri = trim($prefix, '/') . '/' . trim($uri, '/');
        }

        return '/' . trim($uri, '/');
    }

    public function group(array $attributes, \Closure $callback): void
    {
        $previousGroup = $this->currentGroup;

        $this->currentGroup = array_merge($previousGroup, $attributes);

        $callback($this);

        $this->currentGroup = $previousGroup;
    }

    public function setPrefix(string $prefix): self
    {
        $this->currentGroup['prefix'] = $prefix;
        return $this;
    }

    public function setMiddleware(array $middleware): self
    {
        $this->currentGroup['middleware'] = $middleware;
        return $this;
    }

    public function resource(string $uri, string $controller): void
    {
        $this->get($uri, [$controller, 'index']);
        $this->get($uri . '/show', [$controller, 'show']);
        $this->post($uri . '/add', [$controller, 'store']);
        $this->put($uri . '/update', [$controller, 'update']);
        $this->put($uri . '/delete', [$controller, 'delete']);
        $this->delete($uri . '/destroy', [$controller, 'destroy']);
    }

    public function file(string $uri, string $path): void
    {
        $this->get($uri, function () use ($path) {
            if (! file_exists($path)) {
                abort(404);
            }

            $response = new Response();
            return $response->file($path);
        });
    }

    public function version(string $version): self
    {
        $this->version = $version;
        return $this;
    }

    public function dispatch(Request $request): Response
    {
        $method = $request->method();
        $uri    = $request->path();

        foreach ($this->routes as $route) {
            if ($route->matches($method, $uri)) {
                return $this->runRoute($route, $request);
            }
        }

        abort(404, 'Route not found');
    }

    private function runRoute(Route $route, Request $request): Response
    {
        $parameters = $route->bindParameters($request->path());

        foreach ($parameters as $key => $value) {
            $request->setAttribute($key, $value);
        }

        $action = $route->getAction();

        if ($action instanceof \Closure) {
            $result = call_user_func_array($action, $parameters);
        } elseif (is_array($action)) {
            [$controller, $method] = $action;
            $controllerInstance    = new $controller();
            $result                = call_user_func_array([$controllerInstance, $method], $parameters);
        } elseif (is_string($action) && strpos($action, '@') !== false) {
            [$controller, $method] = explode('@', $action);
            $controllerInstance    = new $controller();
            $result                = call_user_func_array([$controllerInstance, $method], $parameters);
        } elseif (is_string($action)) {
            $result = call_user_func_array($action, $parameters);
        } else {
            abort(500, 'Invalid route action');
        }

        return $this->prepareResponse($result);
    }

    private function prepareResponse($result): Response
    {
        if ($result instanceof Response) {
            return $result;
        }

        if (is_array($result) || is_object($result)) {
            $response = new Response();
            return $response->json($result);
        }

        $response = new Response();
        return $response->setContent($result);
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function getRoutesByMethod(string $method): array
    {
        return array_filter($this->routes, fn($route) => $route->getMethod() === strtoupper($method));
    }

    public function getRoutesByName(string $name): ?Route
    {
        foreach ($this->routes as $route) {
            if ($route->getName() === $name) {
                return $route;
            }
        }

        return null;
    }
}
