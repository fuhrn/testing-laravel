<?php

namespace Tests\Feature;

use App\Concert;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PublishConcertTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @group
     * @test
     */
    public function a_promoter_can_publish_their_own_concert()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->states('unpublished')->create([
           'user_id' => $user->id,
            'ticket_quantity' => 3,
        ]);

        $response = $this->actingAs($user)->post('/backstage/published-concerts', [
            'concert_id' => $concert->id,
        ]);

        $response->assertRedirect('/backstage/concerts');
        $this->assertTrue($concert->fresh()->isPublished());
        $this->assertEquals(3, $concert->fresh()->ticketsRemaining());
    }

    /**
     * @test
     */
    public function a_concert_can_only_be_published_once()
    {

        $user = factory(User::class)->create();
        $concert = \ConcertFactoryHelper::createPublished([
            'user_id' => $user->id,
            'ticket_quantity' => 3,
        ]);

        $response = $this->actingAs($user)->post('/backstage/published-concerts', [
            'concert_id' => $concert->id,
        ]);

//        $response->assertStatus(422);
        $this->assertEquals(3, $concert->fresh()->ticketsRemaining());
    }

    /** @test */
    function a_promoter_cannot_publish_other_concerts()
    {
        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();
        $concert = factory(Concert::class)->states('unpublished')->create([
            'user_id' => $otherUser->id,
            'ticket_quantity' => 3,
        ]);

        $response = $this->actingAs($user)->post('/backstage/published-concerts', [
            'concert_id' => $concert->id,
        ]);

        $response->assertStatus(404);
        $concert = $concert->fresh();
        $this->assertFalse($concert->isPublished());
        $this->assertEquals(0, $concert->ticketsRemaining());
    }

    /** @test */
    function a_guest_cannot_publish_concerts()
    {

        $concert = factory(Concert::class)->states('unpublished')->create([
            'ticket_quantity' => 3,
        ]);

        $response = $this->post('/backstage/published-concerts', [
            'concert_id' => $concert->id,
        ]);

        $response->assertRedirect('/login');
        $concert = $concert->fresh();
        $this->assertFalse($concert->isPublished());
        $this->assertEquals(0, $concert->ticketsRemaining());
    }

    /** @test */
    function concerts_that_do_not_exist_cannot_be_published()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post('/backstage/published-concerts', [
            'concert_id' => 999,
        ]);

        $response->assertStatus(404);
    }
}
