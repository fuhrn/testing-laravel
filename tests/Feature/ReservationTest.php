<?php

namespace Tests\Feature;

use App\Billing\FakePaymentGateway;
use App\Concert;
use App\Reservation;
use App\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * group
     * @return void
     */
    public function calculating_the_total_cost()
    {
        $tickets = collect([
            (object) ['price' => 1200],
            (object) ['price' => 1200],
            (object) ['price' => 1200],
        ]);

        $reservation = new Reservation($tickets, 'jonn@example.com');

        $this->assertEquals(3600, $reservation->totalCost());
    }

    /**
     * @test
     * group
     * @return void
     */
    public function retrieving_the_reservations_tickets()
    {
        $tickets = collect([
            (object) ['price' => 1200],
            (object) ['price' => 1200],
            (object) ['price' => 1200],
        ]);

        $reservation = new Reservation($tickets, 'jonn@example.com');

        $this->assertEquals($tickets, $reservation->tickets());

    }

    /**
     * @test
     * group
     * @return void
     */
    public function retrieving_the_customer_email()
    {
//        $tickets = collect();

        $reservation = new Reservation(collect(), 'john@example.com');

        $this->assertEquals('john@example.com', $reservation->email());

    }
    /**
     * @test
     * group
     */
    public function reserved_tickets_are_released_when_a_reservation_is_cancelled()
    {
//        $ticket1 = Mockery::mock(Ticket::class);
//        $ticket1->shouldReceive('release')->once();

//        $ticket2 = Mockery::mock(Ticket::class);
//        $ticket2->shouldReceive('release')->once();
//
//        $ticket3 = Mockery::mock(Ticket::class);
//        $ticket3->shouldReceive('release')->once();

        $tickets = collect([
            Mockery::spy(Ticket::class),
            Mockery::spy(Ticket::class),
            Mockery::spy(Ticket::class),
        ]);

        $reservation = new Reservation($tickets,'jonn@example.com');

        $reservation->cancel();

        foreach($tickets as $ticket) {
            $ticket->shouldHaveReceived('release');
        }
    }

    /**
     * @test
     * group
     */
    public function completing_a_reservation()
    {
        $concert = factory(Concert::class)->create(['ticket_price' => 1200]);
        $tickets = factory(Ticket::class, 3)->create(['concert_id' => $concert->id]);
        $reservation = new Reservation($tickets, 'john@example.com');
        $paymentGateway = new FakePaymentGateway();

        $order = $reservation->complete($paymentGateway, $paymentGateway->getValidTestToken());

        $this->assertEquals('john@example.com', $order->email);
        $this->assertEquals(3, $order->ticketQuantity());
        $this->assertEquals(3600, $order->amount);
        $this->assertEquals(3600, $paymentGateway->totalCharges());
    }
}
