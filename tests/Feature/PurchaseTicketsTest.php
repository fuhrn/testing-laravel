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

    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function customer_can_purchase_concert_tickets()
    {
        $this->withoutExceptionHandling();


        $concert = factory(Concert::class)->create(['ticket_price' => 3250]);

        $response = $this->postJson('/concerts/'.$concert->id.'/orders', [
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
     * A basic feature test example.
     * @test
     * @return void
     */
    public function can_order_concert_tickets()
    {
        $concert = factory(Concert::class)->create();

        $order = $concert->orderTickets('jane@example.com', 3);

        $this->assertEquals('jane@example.com', $order->email);
        $this->assertEquals(3, $order->tickets()->count());
    }

    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function email_is_required_to_purchase_tickets()
    {
//        $this->withoutExceptionHandling();

        $concert = factory(Concert::class)->create();


        $response = $this->postJson('/concerts/'.$concert->id.'/orders', [
//            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $response->assertStatus(422)
                 ->assertJsonMissing([
                     'john@example.com'
                    ]
                 );
    }
}
