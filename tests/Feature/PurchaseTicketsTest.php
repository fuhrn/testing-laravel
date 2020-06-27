<?php

namespace Tests\Feature;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;
use App\Concert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PurchaseTicketsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentGateway = new FakePaymentGateway();
        $this->app->instance(PaymentGateway::class, $this->paymentGateway);
    }

    public function orderTickets($concert, $params)
    {
        return $this->postJson('/concerts/'.$concert->id.'/orders', $params);
    }

    public function assertValidationError($response, $field)
    {
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([$field]);
    }

    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function customer_can_purchase_tickets_to_a_published_concert()
    {
        $this->withoutExceptionHandling();


        $concert = factory(Concert::class)->states('published')->create(['ticket_price' => 3250]);

        $response = $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $response->assertStatus(201);

        $this->assertEquals(9750, $this->paymentGateway->totalCharges());

        $order = $concert->orders()->where('email', 'john@example.com')->first();
        $this->assertNotNull($order);
        $this->assertEquals(3, $order->tickets()->count());
    }

    /**
     * @group failed
     * @test
     * @return void
     */
    public function cannot_purchase_tickets_to_an_unpublished_concert()
    {
//        $this->withoutExceptionHandling();

        $concert = factory(Concert::class)->states('unpublished')->create();

        $response = $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $response->assertStatus(404);
        $this->assertEquals(0, $concert->orders()->count());
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
    }

    /**
     * @group
     * @test
     * @return void
     */
    public function an_order_is_not_created_if_payment_fails()
    {
        $this->withoutExceptionHandling();

        $concert = factory(Concert::class)->states('published')->create(['ticket_price' => 3250]);

        $response = $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => 'invalid-payment-token',
        ]);

        $response->assertStatus(422);
        $order = $concert->orders()->where('email', 'john@example.com')->first();
        $this->assertNull($order);
    }


    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function can_order_concert_tickets()
    {
        $concert = factory(Concert::class)->states('published')->create();

        $order = $concert->orderTickets('jane@example.com', 3);

        $this->assertEquals('jane@example.com', $order->email);
        $this->assertEquals(3, $order->tickets()->count());
    }

    /**
    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function email_is_required_to_purchase_tickets()
    {
//        $this->withoutExceptionHandling();

        $concert = factory(Concert::class)->states('published')->create();


        $response = $this->orderTickets($concert, [
//            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertValidationError($response, 'email');
    }

    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function email_must_be_valid_to_purchase_tickets()
    {
//        $this->withoutExceptionHandling();

        $concert = factory(Concert::class)->states('published')->create();

        $response = $this->orderTickets($concert, [
            'email' => 'not_a_valid_email',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertValidationError($response, 'email');
    }

    /**
     *
     *//**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function ticket_quantity_is_required_to_purchase_tickets()
    {
//        $this->withoutExceptionHandling();

        $concert = factory(Concert::class)->states('published')->create();


        $response = $this->orderTickets($concert, [
            'email' => 'not_a_valid_email',
//            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertValidationError($response, 'ticket_quantity');
    }

    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function ticket_quantity_must_be_at_least_1_to_purchase_ticket()
    {
//        $this->withoutExceptionHandling();

        $concert = factory(Concert::class)->states('published')->create();

        $response = $this->orderTickets($concert, [
            'email' => 'not_a_valid_email',
            'ticket_quantity' => 0,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertValidationError($response, 'ticket_quantity');
    }

    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function payment_token_is_required()
    {
//        $this->withoutExceptionHandling();

        $concert = factory(Concert::class)->states('published')->create();

        $response = $this->orderTickets($concert, [
            'email' => 'not_a_valid_email',
            'ticket_quantity' => 0,
//            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertValidationError($response, 'payment_token');
    }
}
