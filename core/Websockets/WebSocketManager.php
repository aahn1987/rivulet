<?php
namespace Rivulet\Websockets;

use Rivulet\Websockets\Drivers\PusherDriver;

class WebSocketManager
{
    private array $drivers = [];
    private array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config ?: config('websockets', []);
        $this->initializeDrivers();
    }

    private function initializeDrivers(): void
    {
        if (env('PUSHER_APP_ID') && env('PUSHER_APP_KEY') && env('PUSHER_APP_SECRET')) {
            $this->drivers['pusher'] = new PusherDriver(
                env('PUSHER_APP_ID'),
                env('PUSHER_APP_KEY'),
                env('PUSHER_APP_SECRET'),
                env('PUSHER_APP_CLUSTER', 'mt1')
            );
        }

        if (env('WEBSOCKET_SERVER_ENABLED', false)) {
            $this->drivers['ratchet'] = new WebSocketServer([
                'port' => env('WEBSOCKET_PORT', 8080),
                'host' => env('WEBSOCKET_HOST', '0.0.0.0'),
            ]);
        }
    }

    public function driver(string $name = null)
    {
        $name = $name ?? $this->getDefaultDriver();

        if (! isset($this->drivers[$name])) {
            throw new \RuntimeException("WebSocket driver {$name} not configured");
        }

        return $this->drivers[$name];
    }

    public function getDefaultDriver(): string
    {
        return $this->config['default'] ?? 'pusher';
    }

    public function broadcast(string $channel, string $event, $data): bool
    {
        $driver = $this->driver();

        if ($driver instanceof WebSocketServer) {
            $driver->broadcast($channel, ['event' => $event, 'data' => $data]);
            return true;
        }

        if ($driver instanceof PusherDriver) {
            return $driver->trigger($channel, $event, $data);
        }

        return false;
    }

    public function authenticate(string $channel, string $socketId, array $data = []): array
    {
        $driver = $this->driver();

        if ($driver instanceof PusherDriver) {
            return $driver->authenticate($channel, $socketId, $data);
        }

        return [];
    }

    public function startServer(): void
    {
        $driver = $this->driver('ratchet');

        if ($driver instanceof WebSocketServer) {
            $driver->start();
        }
    }

    public function getChannels(): array
    {
        $driver = $this->driver();

        if ($driver instanceof PusherDriver) {
            return $driver->getChannels();
        }

        return [];
    }

    public function getChannelInfo(string $channel): array
    {
        $driver = $this->driver();

        if ($driver instanceof PusherDriver) {
            return $driver->getChannelInfo($channel);
        }

        return [];
    }

    public function getPresenceUsers(string $channel): array
    {
        $driver = $this->driver();

        if ($driver instanceof PusherDriver) {
            return $driver->getPresenceUsers($channel);
        }

        return [];
    }
}
