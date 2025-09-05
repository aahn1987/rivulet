<?php
namespace Rivulet\Console\Commands;

use Rivulet\Console\Command;

class RoutesClearCommand extends Command
{
    protected string $name        = 'routes:clear';
    protected string $description = 'Remove the route cache file';

    public function execute(array $args): void
    {
        $this->info('Clearing route cache…');

        $file = storageLocation('framework/cache/routes.cache');
        if (file_exists($file)) {
            unlink($file);
            $this->success('Route cache cleared.');
        } else {
            $this->warning('No route cache found.');
        }
    }
}
