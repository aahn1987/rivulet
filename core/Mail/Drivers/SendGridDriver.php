<?php
namespace Rivulet\Mail\Drivers;

class SendGridDriver
{
    private string $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function send(array $message): bool
    {
        $data = [
            'personalizations' => [
                [
                    'to' => array_map(fn($email) => ['email' => $email], (array) $message['to']),
                ],
            ],
            'from'             => [
                'email' => $message['from'],
                'name'  => $message['from_name'] ?? '',
            ],
            'subject'          => $message['subject'],
            'content'          => [
                [
                    'type'  => isset($message['html']) ? 'text/html' : 'text/plain',
                    'value' => $message['html'] ?? $message['body'],
                ],
            ],
        ];

        if (! empty($message['cc'])) {
            $data['personalizations'][0]['cc'] = array_map(fn($email) => ['email' => $email], (array) $message['cc']);
        }

        if (! empty($message['bcc'])) {
            $data['personalizations'][0]['bcc'] = array_map(fn($email) => ['email' => $email], (array) $message['bcc']);
        }

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => 'https://api.sendgrid.com/v3/mail/send',
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $this->apiKey,
                'Content-Type: application/json',
            ],
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode >= 200 && $httpCode < 300;
    }
}
