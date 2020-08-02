<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class PromoterLoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     * @test
     * @group
     * @return void
     */
    public function logging_in_with_valid_credentials()
    {
        $this->withoutExceptionHandling();

        $user =  factory(User::class)->create([
            'email' => 'jane@example.com',
            'password' => bcrypt('super-secret-password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'jane@example.com',
            'password' => 'super-secret-password'
        ]);

        $response->assertRedirect('/backstage/concerts');

        $this->assertTrue(Auth::check());
        $this->assertTrue(Auth::user()->is($user));

    }

    /**
     * A basic feature test example.
     * @test
     * @group
     * @return void
     */
    public function logging_in_with_invalid_credentials()
    {
        $this->withoutExceptionHandling();

        $user =  factory(User::class)->create([
            'email' => 'jane@example.com',
            'password' => bcrypt('super-secret-password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'jane@example.com',
            'password' => 'not-the-right-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertFalse(session()->hasOldInput('email')); //borra tanto el mail como psw
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertFalse(Auth::check());
    }

    /**
     * @test
     * @group
     */
    public function logging_in_with_an_account_that_does_not_exist()
    {
        $this->withoutExceptionHandling();

        $response = $this->post('/login', [
            'email' => 'nobody@example.com',
            'password' => 'not-the-right-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertFalse(Auth::check());
    }

    /**
     * @test
     * @group
     */
    public function logging_out_the_current_user()
    {
        Auth::login(factory(User::class)->create());

        $response = $this->post('/logout');

        $response->assertRedirect('/login');
        $this->assertFalse(Auth::check());
    }

}
