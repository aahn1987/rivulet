<?php
namespace Rivulet\Queue\Drivers;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Rivulet\Queue\CallableJob;
use Rivulet\Queue\Job;

class RabbitMQQueue implements QueueDriverInterface
{
    private AMQPStreamConnection $connection;
    private $channel;
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->connect();
    }

    private function connect(): void
    {
        $this->connection = new AMQPStreamConnection(
            $this->config['host'] ?? 'localhost',
            $this->config['port'] ?? 5672,
            $this->config['username'] ?? 'guest',
            $this->config['password'] ?? 'guest',
            $this->config['vhost'] ?? '/'
        );

        $this->channel = $this->connection->channel();
    }

    public function push($job, string $queue = 'default'): string
    {
        return $this->pushRaw($this->createPayload($job), $queue);
    }

    public function later(int $delay, $job, string $queue = 'default'): string
    {
        return $this->pushRaw($this->createPayload($job), $queue, ['delay' => $delay]);
    }

    private function pushRaw(string $payload, string $queue, array $options = []): string
    {
        $id = strRandom(32);

        $this->channel->queue_declare($queue, false, true, false, false);

        $message = new AMQPMessage($payload, [
            'message_id'    => $id,
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            'timestamp'     => time(),
        ]);

        if (isset($options['delay'])) {
            $message->set('application_headers', new \PhpAmqpLib\Wire\AMQPTable([
                'x-delay' => $options['delay'] * 1000,
            ]));
        }

        $this->channel->basic_publish($message, '', $queue);

        return $id;
    }

    private function createPayload($job): string
    {
        if ($job instanceof Job) {
            return serialize($job);
        }

        if (is_callable($job)) {
            return serialize(new CallableJob($job));
        }

        return serialize($job);
    }

    public function pop(string $queue = 'default'): ?Job
    {
        $this->channel->queue_declare($queue, false, true, false, false);

        $message = $this->channel->basic_get($queue);

        if (! $message) {
            return null;
        }

        $job = unserialize($message->getBody());
        $job->setId($message->get('message_id'));
        $job->setQueue($queue);

        $this->channel->basic_ack($message->getDeliveryTag());

        return $job;
    }

    public function size(string $queue = 'default'): int
    {
        list($queueInfo) = $this->channel->queue_declare($queue, true);
        return $queueInfo;
    }

    public function clear(string $queue = 'default'): bool
    {
        $this->channel->queue_purge($queue);
        return true;
    }

    public function release(string $id, int $delay = 0): bool
    {
        return true;
    }

    public function delete(string $id): bool
    {
        return true;
    }

    public function failed(string $id, \Throwable $exception): bool
    {
        return true;
    }

    public function getFailedJobs(): array
    {
        return [];
    }

    public function retry(string $id): bool
    {
        return true;
    }

    public function flushFailed(): bool
    {
        return true;
    }
}
