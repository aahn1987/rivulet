<?php
namespace Rivulet\Console\Commands;

use Rivulet\Console\Command;
use Rivulet\Database\Connection;
use Rivulet\Database\Migrations\Runner;

class DatabaseMigrateCommand extends Command
{
    protected string $name        = 'migrate';
    protected string $description = 'Run database migrations';

    public function execute(array $args): void
    {
        $this->info('Running migrations...');

        try {
            $config     = config('database.connections.mysql');
            $connection = Connection::get($config, 'mysql');
            $runner     = new Runner($connection, basePath('database/Migrations'));

            $runner->run();

            $this->success('Migrations completed successfully');
        } catch (\Exception $e) {
            $this->error('Migration failed: ' . $e->getMessage());
        }
    }
}
