<?php

namespace Tests\Feature;

use App\Concert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ViewConcertListingTest extends TestCase
{
    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function user_cannot_view_unpublished_concert_listings()
    {
        $concert = factory(Concert::class)->create([
            'published_at' => null
        ]);

        $response = $this->get('/concerts/'.$concert->id);

        $response->assertStatus(404);
    }
}
