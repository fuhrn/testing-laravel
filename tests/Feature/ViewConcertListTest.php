<?php

namespace Tests\Feature;

use App\Concert;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ViewConcertListTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @test
     * @group
     */
    public function guests_cannot_view_a_promoters_concert_list()
    {
        $response = $this->get('/backstage/concerts');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /**
     * @test
     * @group
     */
    public function promoters_can_view_a_list_of_their_concerts()
    {
        $user = factory(User::class)->create();
        $concerts = factory(Concert::class, 3)->create([
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->get('/backstage/concerts');

        $response->assertStatus(200);
        $this->assertTrue($response->original->getData()['concerts']->contains($concerts[0]));
        $this->assertTrue($response->original->getData()['concerts']->contains($concerts[1]));
        $this->assertTrue($response->original->getData()['concerts']->contains($concerts[2]));

    }
}
