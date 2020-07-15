<?php

namespace Tests\Feature;

use App\Billing\PaymentFailedException;
use App\Billing\StripePaymentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Stripe\Stripe;
use Tests\TestCase;

class StripePaymentGatewayTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->lastCharge = $this->lastCharge();
    }

    private function lastCharge()
    {
        return $lastCharge = \Stripe\Charge::all(
            ['limit' => 1],
            ['api_key' => config('services.stripe.secret')]
        )['data'][0];
    }

    private function newCharges()
    {
        return \Stripe\Charge::all(
            [
                'limit' => 1,
                'ending_before' => $this->lastCharge->id,
            ],
            ['api_key' => config('services.stripe.secret')]
        )['data'];
    }

    protected function getPaymentGateway()
    {
        return new StripePaymentGateway(config('services.stripe.secret'));
    }

    /**
     * @test
     * @group 1
     */
    public function charges_with_a_valid_payment_token_are_successful()
    {
        $paymentGateway = $this->getPaymentGateway();
        $charge = $paymentGateway->lastCharge();

        $newCharges = $paymentGateway->newChargesDuring(function () {
            $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());
        });


        $this->assertCount(1, $newCharges);
        $this->assertEquals(2500, $newCharges[0]->sum());
    }

    /**
     * @test
     * @return void
     */
    public function charges_with_an_invalid_payment_token_fail()
    {
        try {
            $paymentGateway = new StripePaymentGateway(config('services.stripe.secret'));
            $paymentGateway->charge(2500, 'invalid-payment-token');
        } catch (PaymentFailedException $e) {
            $this->assertCount(0, $this->newCharges());
            return;
        }

        $this->fail("Charging with an invalid payment token did not throw a PaymentFailedException.");
    }
    /**
     * @return string
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function validToken()
    {
        return \Stripe\Token::create([
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 1,
                'exp_year' => date('Y') + 1,
                'cvc' => '123',
            ],
        ], ['api_key' => config('services.stripe.secret')])->id;
    }
}
