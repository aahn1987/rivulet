<?php
namespace Rivulet\Console\Commands;

use Rivulet\Console\Command;

class LogsClearCommand extends Command
{
    protected string $name        = 'logs:clear';
    protected string $description = 'Clear application logs';

    public function execute(array $args): void
    {
        $this->info('Clearing logs...');

        $logPath = storageLocation('logs');

        if (is_dir($logPath)) {
            $files = glob($logPath . '/*.log');
            foreach ($files as $file) {
                if (is_file($file)) {
                    file_put_contents($file, '');
                }
            }
        }

        $this->success('Logs cleared successfully');
    }
}
