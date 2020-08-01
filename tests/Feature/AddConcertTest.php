<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;
use Carbon\Carbon;

class AddConcertTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * @group 1
     * @test
     */
    public function promoters_can_view_the_add_concert_form()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get('/backstage/concerts/new');

        $response->assertStatus(200);
    }

    /**
     * @group 1
     * @test
     */
    public function guests_cannot_view_the_add_concert_form()
    {
//        $this->withoutExceptionHandling();

        $response = $this->get('/backstage/concerts/new');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }
}
