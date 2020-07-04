<?php

namespace App\Billing;
use Illuminate\Support\Facades\Http;


use Stripe\Charge;

class StripePaymentGateway implements PaymentGateway
{
    private $apiKey;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }
    public function charge($amount, $token)
    {
        Charge::create([
            'amount' => $amount,
            'source' => $token,
            'currency' => 'usd'
        ], ['api_key' => $this->apiKey]);
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
