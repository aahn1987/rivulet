<?php
namespace Rivulet\Websockets;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Rivulet\Events\Dispatcher;

class WebSocketServer implements MessageComponentInterface
{
    private $clients;
    private Dispatcher $dispatcher;
    private array $config;
    private array $connectionData = [];

    public function __construct(array $config = [])
    {
        $this->clients    = new \SplObjectStorage;
        $this->dispatcher = new Dispatcher();
        $this->config     = $config;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);

        // Store connection data with proper getter methods

        $resourceId                        = isset($conn->resourceId) ? $conn->resourceId : spl_object_id($conn);
        $this->connectionData[$resourceId] = [
            'resourceId'    => $resourceId,
            'remoteAddress' => method_exists($conn, 'getRemoteAddress') ? $conn->getRemoteAddress() : 'unknown',
            'channels'      => [],
        ];

        $this->dispatcher->dispatch('websocket.connected', [
            'connection'    => $conn,
            'resourceId'    => $resourceId,
            'remoteAddress' => method_exists($conn, 'getRemoteAddress') ? $conn->getRemoteAddress() : 'unknown',
        ]);

        logs()->info("New WebSocket connection: {$resourceId}");
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);

        if (! $data || ! isset($data['event'])) {
            $from->send(json_encode(['error' => 'Invalid message format']));
            return;
        }

        $resourceId = isset($from->resourceId) ? $from->resourceId : spl_object_id($from);
        $this->dispatcher->dispatch('websocket.message', [
            'connection' => $from,
            'resourceId' => $resourceId,
            'message'    => $data,
            'raw'        => $msg,
        ]);

        $this->handleEvent($from, $data);

        logs()->info("WebSocket message from {$resourceId}", $data);
    }

    private function handleEvent(ConnectionInterface $from, array $data): void
    {
        $event   = $data['event'];
        $payload = $data['data'] ?? [];

        switch ($event) {
            case 'subscribe':
                $this->handleSubscribe($from, $payload);
                break;

            case 'unsubscribe':
                $this->handleUnsubscribe($from, $payload);
                break;

            case 'broadcast':
                $this->handleBroadcast($from, $payload);
                break;

            case 'ping':
                $from->send(json_encode(['event' => 'pong', 'data' => []]));
                break;

            default:
                $this->dispatcher->dispatch('websocket.custom.' . $event, [
                    'connection' => $from,
                    'data'       => $payload,
                ]);
        }
    }

    private function handleSubscribe(ConnectionInterface $conn, array $data): void
    {
        $channel = $data['channel'] ?? null;

        if (! $channel) {
            $conn->send(json_encode(['error' => 'Channel not specified']));
            return;
        }

        $resourceId = isset($conn->resourceId) ? $conn->resourceId : spl_object_id($conn);
        if (! isset($this->connectionData[$resourceId])) {
            $this->connectionData[$resourceId] = [
                'resourceId'    => $resourceId,
                'remoteAddress' => method_exists($conn, 'getRemoteAddress') ? $conn->getRemoteAddress() : 'unknown',
                'channels'      => [],
            ];
        }

        $this->connectionData[$resourceId]['channels'][] = $channel;

        $this->dispatcher->dispatch('websocket.subscribed', [
            'connection' => $conn,
            'channel'    => $channel,
        ]);

        $conn->send(json_encode([
            'event' => 'subscribed',
            'data'  => ['channel' => $channel],
        ]));
    }

    private function handleUnsubscribe(ConnectionInterface $conn, array $data): void
    {
        $channel = $data['channel'] ?? null;

        if (! $channel) {
            $conn->send(json_encode(['error' => 'Channel not specified']));
            return;
        }

        $resourceId = isset($conn->resourceId) ? $conn->resourceId : spl_object_id($conn);
        if (isset($this->connectionData[$resourceId]['channels'])) {
            $this->connectionData[$resourceId]['channels'] = array_diff(
                $this->connectionData[$resourceId]['channels'],
                [$channel]
            );
        }

        $this->dispatcher->dispatch('websocket.unsubscribed', [
            'connection' => $conn,
            'channel'    => $channel,
        ]);

        $conn->send(json_encode([
            'event' => 'unsubscribed',
            'data'  => ['channel' => $channel],
        ]));
    }

    private function handleBroadcast(ConnectionInterface $from, array $data): void
    {
        $channel = $data['channel'] ?? null;
        $message = $data['message'] ?? null;

        if (! $channel || ! $message) {
            $from->send(json_encode(['error' => 'Channel and message required']));
            return;
        }

        $this->broadcast($channel, $message, $from);
    }

    public function broadcast(string $channel, $data, ConnectionInterface $sender = null): void
    {
        $message = json_encode([
            'event'     => 'broadcast',
            'channel'   => $channel,
            'data'      => $data,
            'timestamp' => time(),
        ]);

        foreach ($this->clients as $client) {
            $resourceId = isset($client->resourceId) ? $client->resourceId : spl_object_id($client);
            if ($sender && $client === $sender) {
                continue;
            }

            if (isset($this->connectionData[$resourceId]['channels']) &&
                in_array($channel, $this->connectionData[$resourceId]['channels'])) {
                $client->send($message);
            }
        }

        $this->dispatcher->dispatch('websocket.broadcast', [
            'channel' => $channel,
            'data'    => $data,
            'sender'  => $sender,
        ]);
    }

    public function sendToConnection($connectionId, $data): void
    {
        foreach ($this->clients as $client) {
            $resourceId = isset($client->resourceId) ? $client->resourceId : spl_object_id($client);
            if ($resourceId == $connectionId) {
                $client->send(json_encode($data));
                break;
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);

        $resourceId = isset($conn->resourceId) ? $conn->resourceId : spl_object_id($conn);
        unset($this->connectionData[$resourceId]);

        $this->dispatcher->dispatch('websocket.disconnected', [
            'connection' => $conn,
            'resourceId' => $resourceId,
        ]);

        logs()->info("WebSocket disconnected: {$resourceId}");
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        logs()->error("WebSocket error: " . $e->getMessage());
        $conn->close();
    }

    public function getClients(): \SplObjectStorage
    {
        return $this->clients;
    }

    public function getDispatcher(): Dispatcher
    {
        return $this->dispatcher;
    }

    public function start(int $port = 8080, string $host = '0.0.0.0'): void
    {
        $server = new \Ratchet\Server\IoServer(
            new \Ratchet\Http\HttpServer(
                new \Ratchet\WebSocket\WsServer($this)
            ),
            new \React\Socket\Server("{$host}:{$port}")
        );

        logs()->info("WebSocket server started on {$host}:{$port}");
        $server->run();
    }
}
