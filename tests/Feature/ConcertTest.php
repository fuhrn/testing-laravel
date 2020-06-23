<?php

namespace Tests\Feature;

use App\Concert;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ConcertTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function can_get_formatted_date()
    {
        $concert = factory(Concert::class)->create([
            'date' => Carbon::parse('2016-12-01 8:00pm')
        ]);


        $date = $concert->formatted_date;

        $this->assertEquals('December 1, 2016', $date);
    }
}
