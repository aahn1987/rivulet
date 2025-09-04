<?php
namespace Rivulet\Notifications\Drivers;

class SmsDriver
{
    private string $sid;
    private string $authToken;
    private string $phoneNumber;

    public function __construct(string $sid, string $authToken, string $phoneNumber)
    {
        $this->sid         = $sid;
        $this->authToken   = $authToken;
        $this->phoneNumber = $phoneNumber;
    }

    public function send(array $data): bool
    {
        $ch = curl_init();

        $url  = "https://api.twilio.com/2010-04-01/Accounts/{$this->sid}/Messages.json";
        $auth = base64_encode($this->sid . ':' . $this->authToken);

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Basic ' . $auth,
                'Content-Type: application/x-www-form-urlencoded',
            ],
            CURLOPT_POSTFIELDS     => http_build_query([
                'From' => $this->phoneNumber,
                'To'   => $data['to'],
                'Body' => $data['body'],
            ]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode === 201;
    }
}
