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
     * A basic feature test example.
     * @test
     * group 5
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
}
