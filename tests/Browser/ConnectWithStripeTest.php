<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ConnectWithStripeTest extends DuskTestCase
{
    /**
     * @test
     * @group 1
     */
    public function connecting_a_stripe_account_successfully()
    {
        $user = factory(User::class)->create([
            'stripe_account_id' => null,
            'stripe_access_token' => null,
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
            ->visit('/backstage/stripe-connect/authorize')
                    ->assertUrlIs('https://connect.stripe.com/oauth/authorize')
                    ->assertQueryStringHas('response_type', 'code')
                    ->assertQueryStringHas('scope', 'read_write')
                    ->assertQueryStringHas('client_id', config('services.stripe.client_id'));
        });
    }
}
