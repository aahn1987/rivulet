<?php
namespace Rivulet\Console\Commands;

use Rivulet\Console\Command;
use Rivulet\Database\Connection;
use Rivulet\Database\Migrations\Runner;

class DatabaseRollbackCommand extends Command
{
    protected string $name        = 'rollback';
    protected string $description = 'Rollback database migrations';

    public function execute(array $args): void
    {
        $steps = (int) $this->getArg($args, 0, 1);

        $this->info("Rolling back {$steps} migration(s)...");

        try {
            $config     = config('database.connections.mysql');
            $connection = Connection::get($config, 'mysql');
            $runner     = new Runner($connection, basePath('database/Migrations'));

            $runner->rollback($steps);

            $this->success('Rollback completed successfully');
        } catch (\Exception $e) {
            $this->error('Rollback failed: ' . $e->getMessage());
        }
    }
}
