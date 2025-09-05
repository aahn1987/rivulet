<?php
namespace Rivulet\Providers;

use Rivulet\System\Cache\Cache;

class CacheServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->bind('cache', function () {
            return new Cache();
        });

        $this->bind('cache.store', function () {
            return $this->make('cache');
        });
    }

    public function boot(): void
    {
        $this->configureCache();
    }

    protected function configureCache(): void
    {
        $config = [
            'default' => config('cache.driver', 'file'),
            'stores'  => [
                'file'  => [
                    'driver' => 'file',
                    'path'   => config('cache.path', storageLocation('cache')),
                ],
                'redis' => [
                    'driver'     => 'redis',
                    'connection' => 'default',
                ],
            ],
            'prefix'  => config('cache.prefix', 'rivulet'),
        ];

        $cache = $this->make('cache');
        $cache->setConfig($config);
    }
}
