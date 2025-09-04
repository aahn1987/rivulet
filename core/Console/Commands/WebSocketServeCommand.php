<?php
namespace Rivulet\Console\Commands;

use Rivulet\Console\Command;
use Rivulet\Websockets\WebSocketServer;

class WebSocketServeCommand extends Command
{
    protected string $name        = 'ws:serve';
    protected string $description = 'Start WebSocket server';

    public function execute(array $args): void
    {
        $host = $this->getArg($args, 0, '0.0.0.0');
        $port = (int) $this->getArg($args, 1, 8080);

        $this->info("Starting WebSocket server on {$host}:{$port}...");

        try {
            $server = new WebSocketServer();
            $server->start($port, $host);
        } catch (\Exception $e) {
            $this->error('Failed to start WebSocket server: ' . $e->getMessage());
        }
    }
}
