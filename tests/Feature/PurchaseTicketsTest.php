<?php

namespace Tests\Feature;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;
use App\Concert;
use App\OrderConfirmationNumberGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class PurchaseTicketsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var FakePaymentGateway
     */
    private $paymentGateway;
    private $requestA;

    protected function setUp(): void
    {
        parent::setUp();
        $this->requestA = $this->app['request'];
        $this->paymentGateway = new FakePaymentGateway();
        $this->app->instance(PaymentGateway::class, $this->paymentGateway);
    }

    public function orderTickets($concert, $params)
    {
        $this->requestA = $this->app['request'];
        return $this->postJson('/concerts/'.$concert->id.'/orders', $params);
    }

    public function assertValidationError($response, $field)
    {
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([$field]);
    }

    /**
     * @group 1
     * @test
     */
    public function customer_can_purchase_tickets_to_a_published_concert()
    {
        $this->withoutExceptionHandling();

        $orderConfirmationNumberGenerator = Mockery::mock(OrderConfirmationNumberGenerator::class, [
            'generate' => 'ORDERCONFIRMATION1234'
        ]);
        $this->app->instance(OrderConfirmationNumberGenerator::class, $orderConfirmationNumberGenerator);

        $concert = factory(Concert::class)->states('published')->create(['ticket_price' => 3250])->addTickets(3);

        $response = $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $response->assertStatus(201);

        $response->assertJson([
            'confirmation_number' => 'ORDERCONFIRMATION123',
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'amount' => 9750,
        ]);

        $this->assertEquals(9750, $this->paymentGateway->totalCharges());
        $this->assertTrue($concert->hasOrderFor('john@example.com'));
        $this->assertEquals(3, $concert->ordersFor('john@example.com')->first()->ticketQuantity());
    }

    /**
     * @test
     * @group
     */
    public function cannot_purchase_tickets_to_an_unpublished_concert()
    {
//        $this->withoutExceptionHandling();

        $concert = factory(Concert::class)->states('unpublished')->create()->addTickets(3);

        $response = $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $response->assertStatus(404);
        $this->assertFalse($concert->hasOrderFor('john@example.com'));
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
    }

    /**
     * @test
     * @group
     */
    public function cannot_purchase_tickets_another_customer_is_already_trying_to_purchase()
    {
//        $this->withoutExceptionHandling();

        $concert = factory(Concert::class)->states('published')->create([
            'ticket_price' => 1200
        ])->addTickets(3);

        $this->paymentGateway->beforeFirstCharge(function ($paymentGateway) use ($concert) {

            $response = $this->orderTickets($concert, [
                'email' => 'personB@example.com',
                'ticket_quantity' => 1,
                'payment_token' => $this->paymentGateway->getValidTestToken(),
            ]);

// tengo que dejar aqui la instruccion de regenerar el request de personA, porque arriba en orderTickets es unreachable
            $this->app['request'] = $this->requestA;

            $response->assertStatus(422);
            $this->assertFalse($concert->hasOrderfor('personB@example.com'));
            $this->assertEquals(0, $this->paymentGateway->totalCharges());
        });

        $response = $this->orderTickets($concert, [
            'email' => 'personA@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

//        dd($concert->orders()->first()->toArray());
        $this->assertEquals(3600, $this->paymentGateway->totalCharges());
        $this->assertTrue($concert->hasOrderFor('personA@example.com'));
        $this->assertEquals(3, $concert->ordersFor('personA@example.com')->first()->ticketQuantity());
    }


    /**
     * @test
     * @group
     */
    public function an_order_is_not_created_if_payment_fails()
    {
//        $this->withoutExceptionHandling();

        $concert = factory(Concert::class)->states('published')->create(['ticket_price' => 3250])->addTickets(3);

        $response = $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => 'invalid-payment-token',
        ]);

        $response->assertStatus(422);
        $this->assertFalse($concert->hasOrderfor('john@example.com'));
        $this->assertEquals(3, $concert->ticketsRemaining());
    }


    /**
     * @test
     */
    public function can_order_concert_tickets()
    {
        $concert = factory(Concert::class)->states('published')->create();
        $concert->addTickets(3);

        $order = $concert->orderTickets('jane@example.com', 3);

        $this->assertEquals('jane@example.com', $order->email);
        $this->assertEquals(3, $order->tickets()->count());
    }


    /**
     * @test
     * @group
     */
    public function cannot_purchase_more_tickets_than_remain()
    {
        $concert = factory(Concert::class)->states('published')->create()->addTickets(50);

        $response = $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 51,
            'payment_token' => 'invalid-payment-token',
        ]);

        $response->assertStatus(422);
        $this->assertFalse($concert->hasOrderfor('john@example.com'));
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
        $this->assertEquals(50, $concert->ticketsRemaining());
    }


    /**
    /**
     * @test
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
     * @test
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
     * @test
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
     * @test
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
     * @test
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
