<?php
namespace Rivulet\Websockets\Drivers;

use Rivulet\Events\Dispatcher;

class PusherDriver
{
    private string $appId;
    private string $appKey;
    private string $appSecret;
    private string $cluster;
    private Dispatcher $dispatcher;

    public function __construct(string $appId, string $appKey, string $appSecret, string $cluster = 'mt1')
    {
        $this->appId      = $appId;
        $this->appKey     = $appKey;
        $this->appSecret  = $appSecret;
        $this->cluster    = $cluster;
        $this->dispatcher = new Dispatcher();
    }

    public function trigger(string $channel, string $event, $data): bool
    {
        $payload = json_encode([
            'name'    => $event,
            'channel' => $channel,
            'data'    => is_array($data) ? json_encode($data) : $data,
        ]);

        $signature = hash_hmac('sha256', $payload, $this->appSecret);

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => "https://api-{$this->cluster}.pusher.com/apps/{$this->appId}/events",
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->appKey,
                'X-Pusher-Key: ' . $this->appKey,
                'X-Pusher-Signature: ' . $signature,
            ],
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->dispatcher->dispatch('pusher.triggered', [
            'channel' => $channel,
            'event'   => $event,
            'data'    => $data,
            'success' => $httpCode === 200,
        ]);

        return $httpCode === 200;
    }

    public function authenticate(string $channel, string $socketId, array $data = []): array
    {
        $auth = hash_hmac('sha256', $socketId . ':' . $channel, $this->appSecret);

        $response = [
            'auth' => $this->appKey . ':' . $auth,
        ];

        if (! empty($data)) {
            $response['channel_data'] = json_encode($data);
        }

        return $response;
    }

    public function getChannels(): array
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => "https://api-{$this->cluster}.pusher.com/apps/{$this->appId}/channels",
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $this->appKey,
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        return $data['channels'] ?? [];
    }

    public function getChannelInfo(string $channel): array
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => "https://api-{$this->cluster}.pusher.com/apps/{$this->appId}/channels/{$channel}",
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $this->appKey,
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true) ?: [];
    }

    public function getPresenceUsers(string $channel): array
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => "https://api-{$this->cluster}.pusher.com/apps/{$this->appId}/channels/{$channel}/users",
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $this->appKey,
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        return $data['users'] ?? [];
    }
}
