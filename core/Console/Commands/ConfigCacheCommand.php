<?php
namespace Rivulet\Console\Commands;

use Rivulet\Console\Command;

class ConfigCacheCommand extends Command
{
    protected string $name        = 'config:cache';
    protected string $description = 'Cache configuration files';

    public function execute(array $args): void
    {
        $this->info('Caching configuration...');

        $configPath = basePath('config');
        $cachePath  = storageLocation('framework/cache');

        if (! is_dir($cachePath)) {
            mkdir($cachePath, 0755, true);
        }

        $config = [];

        foreach (glob($configPath . '/*.php') as $file) {
            $key          = basename($file, '.php');
            $config[$key] = require $file;
        }

        $cachedContent = '<?php return ' . var_export($config, true) . ';';
        file_put_contents($cachePath . '/config.php', $cachedContent);

        $this->success('Configuration cached successfully');
    }
}
