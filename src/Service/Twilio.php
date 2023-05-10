<?php
// src/Service/Twilio.php

namespace App\Service;

use Twilio\Rest\Client;

class Twilio
{
    private $accountSid;
    private $authToken;
    private $twilioPhoneNumber;

    public function __construct()
    {
        $this->accountSid = "AC1e8690b12cd9c32eaae5df36d8b85cd5";
        $this->authToken = "979e4a1a8f0c8d0441e942f627de0fe0";
        $this->twilioPhoneNumber = +16076083908;
    }

    public function sendMessage(int $toPhoneNumber, string $messageBody): void
    {
        $client = new Client($this->accountSid, $this->authToken);

        $client->messages->create(
            $toPhoneNumber,
            [
                'from' => $this->twilioPhoneNumber,
                'body' => $messageBody,
            ]
        );
    }
}
