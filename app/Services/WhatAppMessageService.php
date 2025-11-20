<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Throwable;

class WhatAppMessageService
{
    public static function sendReferralMessage($referral_request)
    {
        try {
            if ($referral_request['phone']) {
                $url = 'https://messages-sandbox.nexmo.com/v0.1/messages';
                $params = ['to' => ['type' => 'whatsapp', 'number' => $referral_request['phone']],
                    'from' => ['type' => 'whatsapp', 'number' => env('VONAGE_PHONE_NO')],
                    'message' => [
                        'content' => [
                            'type' => 'text',
                            'text' => $referral_request['body'].' please visit this link to view more info about it '.$referral_request['actionURL'],
                        ],
                    ],
                ];
                $headers = ['Authorization' => 'Basic '.base64_encode(env('VONAGE_KEY').':'.env('VONAGE_SECRET'))];
                $client = new \GuzzleHttp\Client(['base_uri' => $url, 'verify' => false]);
                try {
                    $response = $client->request('POST', $url, ['headers' => $headers, 'json' => $params]);
                    $data = $response->getBody();
                    Log::Info($data);

                    return $data;
                } catch (\GuzzleHttp\Exception\RequestException $e) {
                    return $e->getResponse()->getBody()->getContents();
                }
            }
        } catch (Throwable $error) {
            Log::error('Failed to send referral status email. Error message: '.$error->getMessage());
        }
    }

    public static function sendWhatsAppMessage($referral_request)
    {
                 
        $twilioSid = 'ACe7b51bc90a1d19a8204b20db248a0274';
        // $twilioToken = config('app.twilio_auth_token');
        $twilioToken = '4d5f47a2d032bf0cf3f36be2ca9b01a9';
        $twilioPhoneNumber = '+256394704689';
        $recipientPhoneNumber = $referral_request['phone'];
            $url = "https://api.twilio.com/2010-04-01/Accounts/$twilioSid/Messages.json";

            
            $client = new \GuzzleHttp\Client(['verify' => false]);
            try {
            $response = $client->request('POST', $url, [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => 'Basic ' . base64_encode("$twilioSid:$twilioToken"),
                ],
                'form_params' => [
                    'To' => "whatsapp:$recipientPhoneNumber",
                    'From' => "whatsapp:$twilioPhoneNumber",
                    'Body' => $referral_request['whatsApp'],
                ],
            ]);
            $responseData = json_decode($response->getBody(), true);
            if (isset($responseData['sid'])) {
                return $responseData;
                return 'Message sent successfully';
            } else {
                return 'Message not sent, handle the error';
                // You can log the error or display an error message
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $e->getResponse()->getBody()->getContents();
        }
            
    }

    public static function sendWhatsAppMessages($referral_request)
    {

   
        $twilioSid = 'ACe7b51bc90a1d19a8204b20db248a0274';
        // $twilioToken = config('app.twilio_auth_token');
        $twilioToken = '4d5f47a2d032bf0cf3f36be2ca9b01a9';
        $twilioPhoneNumber = '+256394704689';
        $recipientPhoneNumber = $referral_request['phone']; // Replace with the recipient's phone number

        $url = "https://api.twilio.com/2010-04-01/Accounts/$twilioSid/Messages.json";

        $data = [
            'To' => "whatsapp:$recipientPhoneNumber",
            'From' => "whatsapp:$twilioPhoneNumber",
            'Body' => $referral_request['whatsApp'],
        ];

        $headers = [
            'Content-Type: application/x-www-form-urlencoded',
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_USERPWD => "$twilioSid:$twilioToken",
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return $err;
            // cURL error occurred, handle the error
        } else {
            $responseData = json_decode($response, true);

            if (isset($responseData['sid'])) {
                return $responseData;

                return 'Message sent successfully';
            } else {
                return 'Message not sent, handle the error';
            }
        }
    }
}
