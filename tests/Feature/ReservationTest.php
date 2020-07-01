<?php

namespace Tests\Feature;

use App\Concert;
use App\Reservation;
use App\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class ReservationTest extends TestCase
{
//    use RefreshDatabase;

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

        $reservation = new Reservation($tickets);

        $this->assertEquals(3600, $reservation->totalCost());
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

        $reservation = new Reservation($tickets);

        $reservation->cancel();

        foreach($tickets as $ticket) {
            $ticket->shouldHaveReceived('release');
        }
    }
}
