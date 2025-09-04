<?php
namespace Rivulet\Mail\Drivers;

class SendmailDriver
{
    public function send(array $message): bool
    {
        $to      = is_array($message['to']) ? implode(', ', $message['to']) : $message['to'];
        $subject = $message['subject'];
        $body    = $message['html'] ?? $message['body'];

        $headers = [
            'From: ' . $message['from'],
            'Reply-To: ' . ($message['reply_to'] ?? $message['from']),
            'Content-Type: ' . (isset($message['html']) ? 'text/html' : 'text/plain') . '; charset=UTF-8',
            'MIME-Version: 1.0',
        ];

        if (! empty($message['cc'])) {
            $headers[] = 'Cc: ' . (is_array($message['cc']) ? implode(', ', $message['cc']) : $message['cc']);
        }

        if (! empty($message['bcc'])) {
            $headers[] = 'Bcc: ' . (is_array($message['bcc']) ? implode(', ', $message['bcc']) : $message['bcc']);
        }

        foreach ($message['headers'] ?? [] as $name => $value) {
            $headers[] = $name . ': ' . $value;
        }

        $headersString = implode("\r\n", $headers);

        return mail($to, $subject, $body, $headersString);
    }
}
