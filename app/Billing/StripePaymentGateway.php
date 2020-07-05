<?php

namespace App\Billing;

use Stripe\Charge;
use App\Billing\PaymentFailedException;

class StripePaymentGateway implements PaymentGateway
{
    private $apiKey;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }
    public function charge($amount, $token)
    {
        try {
            Charge::create([
                'amount' => $amount,
                'source' => $token,
                'currency' => 'usd'
            ], ['api_key' => $this->apiKey]);
        } catch (\Stripe\Exception\InvalidRequestException  $e) {
            throw new PaymentFailedException;
        }
    }
}

//class StripePaymentGateway implements PaymentGateway
//{
//    private $apiKey;
//
//    public function __construct($apiKey)
//    {
//        $this->apiKey = $apiKey;
//    }
//    public function charge($amount, $token)
//    {
//        dd([$this->apiKey]);
//        (new \GuzzleHttp\Client)->post('https:://api.stripe.com/v1  /charges', [
//            'headers' => [
//                'Authorization' => "Bearer {$this->apiKey}",
//            ],
//            'form_params' => [
//                'amount' => $amount,
//                'source' => $token,
//                'currency' => 'usd',
//                ]
//            ]);

//        Http::withHeaders([
//            'Authorization' => "Bearer {$this->apiKey}",
//        ])->post('https:://api.stripe.com/v1/charges', [
//            'amount' => $amount,
//            'source' => $token,
//            'currency' => 'usd',
//        ]);
//
//    }
//}

//$response = Http::withHeaders([
//    'Authorization' => "Bearer {$this->apiKey}",
//    ])->post('https:://api.stripe.com/v1/charges', [
//    'amount' => $amount,
//    'source' => $token,
//    'currency' => 'usd',
//]);
