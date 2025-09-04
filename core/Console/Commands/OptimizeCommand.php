<?php
namespace Rivulet\Console\Commands;

use Rivulet\Console\Command;

class OptimizeCommand extends Command
{
    protected string $name        = 'optimize';
    protected string $description = 'Cache config, routes, create storage links and clear redundant files';

    public function execute(array $args): void
    {
        $this->info('Optimising framework…');

        // 1. Config cache
        $this->call('config:cache');

        // 2. Routes cache
        $this->call('routes:cache');

        // 3. Storage links
        $this->call('storage:link');

        // 4. Optional housekeeping
        $this->call('cache:clear');
        $this->call('logs:clear');

        $this->success('Optimisation complete!');
    }

    /* Re-use existing commands instead of duplicating logic */
    private function call(string $command): void
    {
        $class = $this->commands[$command] ?? null;
        if (! $class) {
            $this->warning("Skipping {$command} – not registered.");
            return;
        }
        (new $class)->execute([]); // run silently
    }

    /* Map command names to their classes (same keys used in Console.php) */
    private array $commands = [
        'config:cache' => ConfigCacheCommand::class,
        'routes:cache' => RoutesCacheCommand::class,
        'storage:link' => StorageLinkCommand::class,
        'cache:clear'  => CacheClearCommand::class,
        'logs:clear'   => LogsClearCommand::class,
    ];
}
