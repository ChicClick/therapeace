<?php

// INFOBIP_KEY="7f6f96c2d572442a09e00d999d8bbf50-8c96b79d-0967-4ee8-bd38-5cd1dac39919"
// INFOBIP_URL="m38pqj.api.infobip.com"
class SmsSender
{
    private $apiKey;
    private $url;

    public function __construct()
    {
        $this->apiKey = "64659e5bb98d9c1a57469c9a27e079a3";
        $this->url = "https://api.semaphore.co/api/v4/messages";
    }

    public function sendSMS($phoneNumbers, $message)
    {

        $parameters = [
            'apikey' => $this->apiKey, 
            'number' => "+639053455886",
            'message' => $message
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            return;
        }

        curl_close($ch);

        return $result;
    }
}
