<?php
namespace Rivulet\Mail\Drivers;

class MailgunDriver
{
    private string $domain;
    private string $secret;

    public function __construct(string $domain, string $secret)
    {
        $this->domain = $domain;
        $this->secret = $secret;
    }

    public function send(array $message): bool
    {
        $ch = curl_init();

        $data = [
            'from'    => $message['from'],
            'to'      => is_array($message['to']) ? implode(',', $message['to']) : $message['to'],
            'subject' => $message['subject'],
            'text'    => $message['body'] ?? '',
            'html'    => $message['html'] ?? '',
        ];

        if (! empty($message['cc'])) {
            $data['cc'] = is_array($message['cc']) ? implode(',', $message['cc']) : $message['cc'];
        }

        if (! empty($message['bcc'])) {
            $data['bcc'] = is_array($message['bcc']) ? implode(',', $message['bcc']) : $message['bcc'];
        }

        curl_setopt_array($ch, [
            CURLOPT_URL            => "https://api.mailgun.net/v3/{$this->domain}/messages",
            CURLOPT_USERPWD        => "api:{$this->secret}",
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode >= 200 && $httpCode < 300;
    }
}
