<?php
namespace Rivulet\Notifications\Drivers;

class FirebaseDriver
{
    private string $serverKey;
    private string $senderId;

    public function __construct(string $serverKey, string $senderId)
    {
        $this->serverKey = $serverKey;
        $this->senderId  = $senderId;
    }

    public function send(array $data): bool
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => 'https://fcm.googleapis.com/fcm/send',
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: key=' . $this->serverKey,
                'Content-Type: application/json',
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
