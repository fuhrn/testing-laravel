<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Stripe\Stripe;
use Tests\TestCase;

class StripePaymentGatewayTest extends TestCase
{
    /**
     * A basic feature test example.
     * @test
     * @group 1
     */
    public function charges_with_a_valid_payment_token_are_successful()
    {
        $paymentGateway = new StripePaymentGateway;

        $token = \Stripe\Token::create([
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 1,
                'exp_year' => date('Y') + 1,
                'cvc' => '123',
            ],
        ], ['api_key' => config('services.stripe.secret')])->id;

        $paymentGateway->charge(2500, $token);

        $lastCharge = \Stripe\Charge::all(
            ['limit' => 1],
            ['api_key' => config('services.stripe.secret')]
        )['data'][0];

        $this->assertEquals(2500, $lastCharge->amount);
    }
}
