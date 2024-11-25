<?php
$apiKey = getenv('INFOBIP_KEY');
$apiUrl = getenv('INFOBIP_URL');


// INFOBIP_KEY="7f6f96c2d572442a09e00d999d8bbf50-8c96b79d-0967-4ee8-bd38-5cd1dac39919"
// INFOBIP_URL="m38pqj.api.infobip.com"
class SmsSender
{
    private $apiKey; // Infobip API Key
    private $url;    // Infobip API URL

    // Constructor to initialize API key and URL
    public function __construct()
    {
        // $this->apiKey = getenv('INFOBIP_KEY');
        // $this->url = 'https://' . getenv('INFOBIP_URL') . '/sms/1/text/single'; //PROD

        $this->apiKey = "7f6f96c2d572442a09e00d999d8bbf50-8c96b79d-0967-4ee8-bd38-5cd1dac39919";
        $this->url = "https://m38pqj.api.infobip.com/sms/1/text/single"; //DEV
    }

    // Function to send SMS
    public function sendSMS($phoneNumber, $message)
    {
        // Prepare the POST data
        $data = [
            'to' => "+639763860567",   
            'text' => $message      
        ];

        // Initialize cURL
        $ch = curl_init($this->url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: App ' . $this->apiKey,  // Infobip API authentication
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // Execute the cURL request and get the response
        $response = curl_exec($ch);

        // Check for cURL errors
        if(curl_errno($ch)) {
            return 'Error:' . curl_error($ch);
        } else {
            $responseJson = json_decode($response, true);
            if ($responseJson['messages'][0]['status']['groupName'] == 'ACCEPTED') {
                return "SMS sent successfully!";
            } else {
                return "Failed to send SMS. Error: " . $responseJson['messages'][0]['status']['description'];
            }
        }

        curl_close($ch);
    }
}

?>
