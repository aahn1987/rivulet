<?php
namespace Rivulet\Http;

class Kernel
{
    protected array $middleware       = [];
    protected array $middlewareGroups = [];
    protected array $routeMiddleware  = [];

    public function handle(Request $request): Response
    {
        $this->sendRequestThroughRouter($request);

        return $this->dispatch($request);
    }

    protected function sendRequestThroughRouter(Request $request): void
    {
        foreach ($this->middleware as $middleware) {
            $this->callMiddleware($middleware, $request);
        }
    }

    public function dispatch(Request $request): Response
    {
        $router = new Router();
        return $router->dispatch($request);
    }

    protected function callMiddleware(string $middleware, Request $request): void
    {
        if (class_exists($middleware)) {
            $instance = new $middleware();

            if (method_exists($instance, 'handle')) {
                $instance->handle($request, function ($request) {});
            }
        }
    }

    public function pushMiddleware(string $middleware): self
    {
        $this->middleware[] = $middleware;
        return $this;
    }

    public function prependMiddleware(string $middleware): self
    {
        array_unshift($this->middleware, $middleware);
        return $this;
    }

    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    public function addRouteMiddleware(string $name, string $middleware): self
    {
        $this->routeMiddleware[$name] = $middleware;
        return $this;
    }

    public function getRouteMiddleware(string $name): ?string
    {
        return $this->routeMiddleware[$name] ?? null;
    }
}
