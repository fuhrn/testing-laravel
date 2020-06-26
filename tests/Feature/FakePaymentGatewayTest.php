<?php

namespace Tests\Feature;

use App\Billing\PaymentFailedException;
use App\Billing\FakePaymentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FakePaymentGatewayTest extends TestCase
{
    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function charges_with_a_valid_payment_token_are_successful()
    {
        $paymentGateway = new FakePaymentGateway();

        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());

        $this->assertEquals(2500, $paymentGateway->totalCharges());
    }

    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function charges_with_an_invalid_payment_token_fail()
    {
        try{
            $paymentGateway = new FakePaymentGateway();
            $paymentGateway->charge(2500, 'invalid-payment-token');
        } catch (PaymentFailedException $e) {
            return;
        }

        $this->fail();
    }
}
