<?php

namespace App\Services;
use GuzzleHttp\Client;

/**
 * Class TextGuruService.
 */
class TextGuruService
{
    protected $client;
    protected $apiKey;
    protected $senderId;
    protected $url;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = config('services.textguru.api_key');
        $this->senderId = config('services.textguru.sender_id');
        $this->url = config('services.textguru.url');
    }

    /**
     * Send an SMS via TextGuru API.
     *
     * @param string $mobileNumber
     * @param string $message
     * @return array
     */
    public function sendSms($mobileNumber, $message)
    {
        $params = [
            'authkey' => $this->apiKey,
            'mobiles' => $mobileNumber,
            'message' => $message,
            'sender' => $this->senderId,
            'route' => '4',
            'country' => '91' 
        ];

        try {
            $response = $this->client->request('GET', $this->url, [
                'query' => $params,
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

}
