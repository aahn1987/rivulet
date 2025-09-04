<?php
namespace Rivulet\Filesystem\Operations;

class Download
{
    public static function execute(string $url, string $destination): bool
    {
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $dir = dirname($destination);

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $ch = curl_init($url);
        $fp = fopen($destination, 'wb');

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Rivulet Framework');

        $result   = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);
        fclose($fp);

        if (! $result || $httpCode !== 200) {
            unlink($destination);
            return false;
        }

        return true;
    }
}
