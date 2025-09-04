<?php
namespace Rivulet\Console\Commands;

use Rivulet\Console\Command;

class RunServerCommand extends Command
{
    protected string $name        = 'run';
    protected string $description = 'Start development server';

    public function execute(array $args): void
    {
        $host = $this->getArg($args, 0, 'localhost');
        $port = (int) $this->getArg($args, 1, '8000');

        $this->info("Starting Rivulet development server...");
        $this->info("Server running on http://{$host}:{$port}");
        $this->info("Press Ctrl+C to stop the server");

        $publicPath = publicPath();
        $router     = basePath('router.php');

        $command = "php -S {$host}:{$port} -t {$publicPath}";

        if (file_exists($router)) {
            $command .= " {$router}";
        }

        passthru($command);
    }
}
