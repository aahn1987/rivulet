<?php
namespace Rivulet\Console\Commands;

use Rivulet\Console\Command;

class ConfigClearCommand extends Command
{
    protected string $name        = 'config:clear';
    protected string $description = 'Remove the configuration cache';

    public function execute(array $args): void
    {
        $this->info('Clearing configuration cache…');

        $file = storageLocation('framework/cache/config.php');
        if (file_exists($file)) {
            unlink($file);
            $this->success('Configuration cache cleared.');
        } else {
            $this->warning('No config cache found.');
        }
    }
}
