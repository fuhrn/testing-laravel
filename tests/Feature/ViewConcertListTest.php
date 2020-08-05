<?php

namespace Tests\Feature;

use App\Concert;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert;
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
     * @group 1
     */
    public function promoters_can_view_a_list_of_their_concerts()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();

        $publishedConcertA = \ConcertFactoryHelper::createPublished(['user_id' => $user->id]);
        $publishedConcertB = \ConcertFactoryHelper::createPublished(['user_id' => $otherUser->id]);
        $publishedConcertC = \ConcertFactoryHelper::createPublished(['user_id' => $user->id]);

        $unpublishedConcertA = factory(Concert::class)->states('unpublished')->create(['user_id' => $user->id]);
        $unpublishedConcertB = factory(Concert::class)->states('unpublished')->create(['user_id' => $otherUser->id]);
        $unpublishedConcertC = factory(Concert::class)->states('unpublished')->create(['user_id' => $user->id]);


        $response = $this->actingAs($user)->get('/backstage/concerts');

        $response->assertStatus(200);

        $response->data('publichedConcerts')->assertContains($publishedConcertA);
        $response->data('publichedConcerts')->assertNotContains($publishedConcertB);
        $response->data('publichedConcerts')->assertContains($publishedConcertC);
        $response->data('publichedConcerts')->assertNotContains($unpublishedConcertA);
        $response->data('publichedConcerts')->assertNotContains($unpublishedConcertB);
        $response->data('publichedConcerts')->assertNotContains($unpublishedConcertC);

        $response->data('unpublichedConcerts')->assertNotContains($publishedConcertA);
        $response->data('unpublichedConcerts')->assertNotContains($publishedConcertB);
        $response->data('unpublichedConcerts')->assertNotContains($publishedConcertC);
        $response->data('unpublichedConcerts')->assertContains($unpublishedConcertA);
        $response->data('unpublichedConcerts')->assertNotContains($unpublishedConcertB);
        $response->data('unpublichedConcerts')->assertContains($unpublishedConcertC);

    }
}
