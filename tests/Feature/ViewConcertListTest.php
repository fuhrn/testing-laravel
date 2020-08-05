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

        $unpublishedConcertA = \ConcertFactoryHelper::createUnPublished(['user_id' => $user->id]);
        $unpublishedConcertB = \ConcertFactoryHelper::createUnPublished(['user_id' => $otherUser->id]);
        $unpublishedConcertC = \ConcertFactoryHelper::createUnPublished(['user_id' => $user->id]);


        $response = $this->actingAs($user)->get('/backstage/concerts');

        $response->assertStatus(200);

        $response->data('publishedConcerts')->assertEquals([
            $publishedConcertA,
            $publishedConcertC,
        ]);

        $response->data('unpublishedConcerts')->assertEquals([
            $unpublishedConcertA,
            $unpublishedConcertC,
        ]);

    }
}
