<?php
namespace Rivulet\Container;

use Closure;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;

class Container
{
    private array $bindings           = [];
    private array $instances          = [];
    private array $aliases            = [];
    private array $extenders          = [];
    private array $tags               = [];
    private array $resolving          = [];
    private array $reboundCallbacks   = [];
    private array $resolvingCallbacks = [];

    public function bind(string $abstract, $concrete = null, bool $shared = false): void
    {
        $this->dropStaleInstances($abstract);

        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        if (! $concrete instanceof Closure) {
            $concrete = $this->getClosure($abstract, $concrete);
        }

        $this->bindings[$abstract] = compact('concrete', 'shared');

        if ($this->resolved($abstract)) {
            $this->rebound($abstract);
        }
    }

    public function singleton(string $abstract, $concrete = null): void
    {
        $this->bind($abstract, $concrete, true);
    }

    public function instance(string $abstract, $instance)
    {
        $this->removeAbstractAlias($abstract);

        $this->aliases[$abstract] = $abstract;

        $this->instances[$abstract] = $instance;

        if ($this->has($abstract)) {
            $this->rebound($abstract);
        }

        return $instance;
    }

    public function make(string $abstract, array $parameters = [])
    {
        return $this->resolve($abstract, $parameters);
    }

    public function resolve(string $abstract, array $parameters = [], bool $raiseEvents = true)
    {
        $abstract = $this->getAlias($abstract);

        $concrete = $this->getConcrete($abstract);

        if ($this->isBuildable($concrete, $abstract)) {
            $object = $this->build($concrete, $parameters);
        } else {
            $object = $this->make($concrete, $parameters);
        }

        foreach ($this->getExtenders($abstract) as $extender) {
            $object = $extender($object, $this);
        }

        if ($this->isShared($abstract) && ! isset($this->instances[$abstract])) {
            $this->instances[$abstract] = $object;
        }

        if ($raiseEvents) {
            $this->fireResolvingCallbacks($abstract, $object);
        }

        $this->resolving[$abstract] = false;

        return $object;
    }

    protected function isBuildable($concrete, string $abstract): bool
    {
        return $concrete === $abstract || $concrete instanceof Closure;
    }

    public function build($concrete, array $parameters = [])
    {
        if ($concrete instanceof Closure) {
            return $concrete($this, $parameters);
        }

        $reflector = new ReflectionClass($concrete);

        if (! $reflector->isInstantiable()) {
            throw new \RuntimeException("Class {$concrete} is not instantiable");
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return new $concrete;
        }

        $dependencies = $constructor->getParameters();

        $instances = $this->resolveDependencies($dependencies, $parameters);

        return $reflector->newInstanceArgs($instances);
    }

    protected function resolveDependencies(array $dependencies, array $parameters = []): array
    {
        $results = [];

        foreach ($dependencies as $dependency) {
            if (array_key_exists($dependency->getName(), $parameters)) {
                $results[] = $parameters[$dependency->getName()];
                continue;
            }

            $results[] = is_null($dependency->getType()) || $dependency->getType()->isBuiltin()
            ? $this->resolvePrimitive($dependency)
            : $this->resolveClass($dependency);
        }

        return $results;
    }

    protected function resolvePrimitive(ReflectionParameter $parameter)
    {
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        throw new \RuntimeException("Unresolvable dependency resolving [$parameter]");
    }

    protected function resolveClass(ReflectionParameter $parameter)
    {
        try {
            $type = $parameter->getType();
            if ($type instanceof ReflectionNamedType && ! $type->isBuiltin()) {
                return $this->make($type->getName());
            }
            throw new \RuntimeException("Cannot resolve class for parameter {$parameter->getName()}");
        } catch (\Exception $e) {
            if ($parameter->isOptional()) {
                return $parameter->getDefaultValue();
            }

            throw $e;
        }
    }

    public function getConcrete(string $abstract)
    {
        if (isset($this->bindings[$abstract])) {
            return $this->bindings[$abstract]['concrete'];
        }

        return $abstract;
    }

    public function getAlias(string $abstract): string
    {
        return isset($this->aliases[$abstract])
        ? $this->getAlias($this->aliases[$abstract])
        : $abstract;
    }

    public function alias(string $abstract, string $alias): void
    {
        $this->aliases[$alias] = $abstract;
    }

    public function tag($abstracts, $tags): void
    {
        $tags = is_array($tags) ? $tags : [$tags];

        foreach ($tags as $tag) {
            if (! isset($this->tags[$tag])) {
                $this->tags[$tag] = [];
            }

            foreach ((array) $abstracts as $abstract) {
                $this->tags[$tag][] = $abstract;
            }
        }
    }

    public function tagged(string $tag): array
    {
        if (! isset($this->tags[$tag])) {
            return [];
        }

        return array_map(function ($abstract) {
            return $this->make($abstract);
        }, $this->tags[$tag]);
    }

    public function extend(string $abstract, \Closure $closure): void
    {
        $abstract = $this->getAlias($abstract);

        if (isset($this->instances[$abstract])) {
            $this->instances[$abstract] = $closure($this->instances[$abstract], $this);
            $this->rebound($abstract);
        } else {
            $this->extenders[$abstract][] = $closure;
        }
    }

    public function getExtenders(string $abstract): array
    {
        return $this->extenders[$abstract] ?? [];
    }

    public function when(string $concrete): ContextualBindingBuilder
    {
        return new ContextualBindingBuilder($this, $concrete);
    }

    public function factory(string $abstract): \Closure
    {
        return function () use ($abstract) {
            return $this->make($abstract, func_get_args());
        };
    }

    public function flush(): void
    {
        $this->aliases            = [];
        $this->bindings           = [];
        $this->instances          = [];
        $this->tags               = [];
        $this->extenders          = [];
        $this->reboundCallbacks   = [];
        $this->resolvingCallbacks = [];
    }

    public function bound(string $abstract): bool
    {
        return isset($this->bindings[$abstract]) ||
        isset($this->instances[$abstract]) ||
        $this->isAlias($abstract);
    }

    public function resolved(string $abstract): bool
    {
        if ($this->isAlias($abstract)) {
            $abstract = $this->getAlias($abstract);
        }

        return isset($this->instances[$abstract]) ||
            (isset($this->resolving[$abstract]) && $this->resolving[$abstract] === true);
    }

    public function isAlias(string $name): bool
    {
        return isset($this->aliases[$name]);
    }

    public function isShared(string $abstract): bool
    {
        return isset($this->instances[$abstract]) ||
            (isset($this->bindings[$abstract]['shared']) &&
            $this->bindings[$abstract]['shared'] === true);
    }

    public function has(string $id): bool
    {
        return $this->bound($id);
    }

    public function get(string $id)
    {
        try {
            return $this->resolve($id);
        } catch (\Exception $e) {
            if ($this->has($id)) {
                throw $e;
            }

            throw new \RuntimeException("Entry {$id} not found");
        }
    }

    protected function rebound(string $abstract): void
    {
        if (isset($this->reboundCallbacks[$abstract])) {
            $instance = $this->make($abstract);

            foreach ($this->reboundCallbacks[$abstract] as $callback) {
                $callback($this, $instance);
            }
        }
    }

    public function rebounding(string $abstract, \Closure $callback): void
    {
        $this->reboundCallbacks[$abstract][] = $callback;
    }

    protected function getReboundCallbacks(string $abstract): array
    {
        return $this->reboundCallbacks[$abstract] ?? [];
    }

    protected function fireResolvingCallbacks(string $abstract, $object): void
    {
        foreach ($this->getResolvingCallbacks($abstract) as $callback) {
            $callback($object, $this);
        }
    }

    public function resolving(string $abstract, \Closure $callback): void
    {
        $this->resolvingCallbacks[$abstract][] = $callback;
    }

    protected function getResolvingCallbacks(string $abstract): array
    {
        return $this->resolvingCallbacks[$abstract] ?? [];
    }

    protected function dropStaleInstances(string $abstract): void
    {
        unset($this->instances[$abstract], $this->aliases[$abstract]);
    }

    protected function removeAbstractAlias(string $searched): void
    {
        if (! isset($this->aliases[$searched])) {
            return;
        }

        foreach ($this->aliases as $abstract => $alias) {
            if ($alias == $searched) {
                unset($this->aliases[$abstract]);
            }
        }
    }

    private function getClosure(string $abstract, string $concrete): \Closure
    {
        return function ($container, $parameters = []) use ($abstract, $concrete) {
            if ($abstract == $concrete) {
                return $container->build($concrete, $parameters);
            }

            return $container->resolve($concrete, $parameters, false);
        };
    }
}
