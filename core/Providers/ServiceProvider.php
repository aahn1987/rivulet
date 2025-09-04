<?php
namespace Rivulet\Providers;

abstract class ServiceProvider
{
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    abstract public function register(): void;

    public function boot(): void
    {
        //
    }

    protected function bind(string $abstract, $concrete = null, bool $shared = true): void
    {
        $this->app->bind($abstract, $concrete, $shared);
    }

    protected function singleton(string $abstract, $concrete = null): void
    {
        $this->app->singleton($abstract, $concrete);
    }

    protected function make(string $abstract)
    {
        return $this->app->make($abstract);
    }

    protected function config(string $key, $default = null)
    {
        return config($key, $default);
    }

    protected function env(string $key, $default = null)
    {
        return env($key, $default);
    }
}
