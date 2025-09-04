<?php
namespace Rivulet\Console\Commands;

use Rivulet\Console\Command;
use Rivulet\Database\Connection;

class DatabaseSeedCommand extends Command
{
    protected string $name        = 'seed';
    protected string $description = 'Run database seeders';

    public function execute(array $args): void
    {
        $this->info('Running database seeders...');

        try {
            $config     = config('database.connections.mysql');
            $connection = Connection::get($config, 'mysql');

            $seederFiles = glob(basePath('database/Seeders/*.php'));

            foreach ($seederFiles as $file) {
                require_once $file;

                $className     = basename($file, '.php');
                $fullClassName = "\\Database\\Seeders\\{$className}";

                if (class_exists($fullClassName)) {
                    $seeder = new $fullClassName();

                    if (method_exists($seeder, 'run')) {
                        $seeder->run();
                        $this->info("Seeded: {$className}");
                    }
                }
            }

            $this->success('Database seeding completed successfully');
        } catch (\Exception $e) {
            $this->error('Seeding failed: ' . $e->getMessage());
        }
    }
}
