<?php
namespace Rivulet\Notifications\Drivers;

class PusherDriver
{
    private string $appId;
    private string $appKey;
    private string $appSecret;
    private string $cluster;

    public function __construct(string $appId, string $appKey, string $appSecret, string $cluster)
    {
        $this->appId     = $appId;
        $this->appKey    = $appKey;
        $this->appSecret = $appSecret;
        $this->cluster   = $cluster;
    }

    public function send(array $data): bool
    {
        $ch = curl_init();

        $authKey = hash_hmac('sha256', "POST\n/apps/{$this->appId}/events\n" . json_encode($data), $this->appSecret);

        curl_setopt_array($ch, [
            CURLOPT_URL            => "https://api-{$this->cluster}.pusher.com/apps/{$this->appId}/events",
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'X-Pusher-Key: ' . $this->appKey,
                'X-Pusher-Signature: ' . $authKey,
            ],
            CURLOPT_POSTFIELDS     => json_encode($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode === 200;
    }
}
