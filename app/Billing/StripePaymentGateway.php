<?php

namespace App\Billing;

use Stripe\Charge;

class StripePaymentGateway implements PaymentGateway
{
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
