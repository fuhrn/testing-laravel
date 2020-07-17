<?php

namespace Tests\Feature;

use App\Concert;
use App\Order;
use App\Reservation;
use App\Ticket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @group
     */
    public function creating_order_from_tickets_email_and_amount()
    {
        $concert = factory(Concert::class)->create()->addTickets(5);
        $this->assertEquals(5, $concert->ticketsRemaining());

        $order = Order::forTickets($concert->findTickets(3), 'john@example.com', 3600);

        $this->assertEquals('john@example.com', $order->email);
        $this->assertEquals(3, $order->ticketQuantity());
        $this->assertEquals(3600, $order->amount);
        $this->assertEquals(2, $concert->ticketsRemaining());

    }


    /**
     * @test
     * @group
     */
    public function retrieving_an_order_by_confirmation_number()
    {
        $order = factory(Order::class)->create([

            'confirmation_number' => 'ORDERCONFIRMATION1234'
        ]);

        $foundOrder = Order::findByConfirmationNumber('ORDERCONFIRMATION1234');

        $this->assertEquals($order->id, $foundOrder->id);
    }

    /**
     * @test
     * @group 1
     */
    public function retrieving_an_nonexistent_order_by_confirmation_number_throws_an_exception()
    {
        try {
            Order::findByConfirmationNumber('NONEXISTENTCONFIRMATIONNUMBER');
        } catch (ModelNotFoundException $e) {
            return;
        }

        $this->fail('No matchig order was found for the specified confirmation number, but an exception was not thrown');

    }

    /**
     * @test
     * @group
     */
    public function converting_to_an_array()
    {
        $concert = factory(Concert::class)->create(['ticket_price' => 1200])->addTickets(5);
        $order = $concert->orderTickets('jane@example.com', 5);

        $result = $order->toArray();

        $this->assertEquals([
            'email' => 'jane@example.com',
            'ticket_quantity' => 5,
            'amount' => 6000,
        ], $result);
    }

//    /**
//     * @test
//     * @group 1
//     */
//    public function creating_an_order_from_a_reservation()
//    {
//        $concert = factory(Concert::class)->create(['ticket_price' => 1200]);
//        $tickets = factory(Ticket::class, 3)->create(['concert_id' => $concert->id]);
//
//        $reservation = new Reservation($tickets, 'john@example.com');
//
//        $order = Order::fromReservation($reservation);
//
//        $this->assertEquals('john@example.com', $order->email);
//        $this->assertEquals(3, $order->ticketQuantity());
//        $this->assertEquals(3600, $order->amount);
//    }
}
