<?php
namespace Rivulet\Routing;

class Route
{
    private string $method;
    private string $uri;
    private $action;
    private array $middleware = [];
    private array $parameters = [];
    private array $wheres     = [];

    public function __construct(string $method, string $uri, $action)
    {
        $this->method = strtoupper($method);
        $this->uri    = $uri;
        $this->action = $action;
        $this->compileRoute();
    }

    private function compileRoute(): void
    {
        preg_match_all('/\{(.*?)\}/', $this->uri, $matches);

        foreach ($matches[1] as $match) {
            $this->parameters[] = $match;
        }
    }

    public function matches(string $method, string $uri): bool
    {
        if ($this->method !== strtoupper($method)) {
            return false;
        }

        $pattern = preg_replace_callback('/\{(.*?)\}/', function ($matches) {
            $param = $matches[1];
            $regex = $this->wheres[$param] ?? '[^/]+';
            return '(' . $regex . ')';
        }, $this->uri);

        $pattern = '#^' . $pattern . '$#';

        return preg_match($pattern, $uri, $matches) === 1;
    }

    public function bindParameters(string $uri): array
    {
        $pattern = preg_replace_callback('/\{(.*?)\}/', function ($matches) {
            $param = $matches[1];
            $regex = $this->wheres[$param] ?? '[^/]+';
            return '(' . $regex . ')';
        }, $this->uri);

        $pattern = '#^' . $pattern . '$#';

        preg_match($pattern, $uri, $matches);

        $parameters = [];
        foreach ($this->parameters as $index => $param) {
            $parameters[$param] = $matches[$index + 1] ?? null;
        }

        return $parameters;
    }

    public function middleware(array $middleware): self
    {
        $this->middleware = array_merge($this->middleware, $middleware);
        return $this;
    }

    public function where(string $parameter, string $regex): self
    {
        $this->wheres[$parameter] = $regex;
        return $this;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getController(): ?string
    {
        if (is_array($this->action) && isset($this->action[0])) {
            return $this->action[0];
        }

        return null;
    }

    public function getMethodName(): ?string
    {
        if (is_array($this->action) && isset($this->action[1])) {
            return $this->action[1];
        }

        return null;
    }

    public function getClosure()
    {
        if ($this->action instanceof \Closure) {
            return $this->action;
        }

        return null;
    }
}
