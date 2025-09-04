<?php
namespace Rivulet\Notifications\Drivers;

class WhatsappDriver
{
    private string $token;
    private string $phoneNumberId;

    public function __construct(string $token, string $phoneNumberId)
    {
        $this->token         = $token;
        $this->phoneNumberId = $phoneNumberId;
    }

    public function send(array $data): bool
    {
        $ch = curl_init();

        $url = "https://graph.facebook.com/v18.0/{$this->phoneNumberId}/messages";

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $this->token,
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS     => json_encode([
                'messaging_product' => 'whatsapp',
                'to'                => $data['to'],
                'type'              => 'text',
                'text'              => ['body' => $data['body']],
            ]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode === 200;
    }
}
