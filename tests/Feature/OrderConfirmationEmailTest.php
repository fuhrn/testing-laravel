<?php

namespace Tests\Feature;

use App\Mail\OrderConfirmationEmail;
use App\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderConfirmationEmailTest extends TestCase
{
    /**
     * @test
     * group 2
     */
    public function email_contains_a_link_to_the_order_confirmation_page()
    {
        $order = factory(Order::class)->make([
            'confirmation_number' => 'ORDERCONFIRMATION1234'
        ]);

        $rendered = (new OrderConfirmationEmail($order))->render();

        $this->assertStringContainsString(url('/orders/ORDERCONFIRMATION1234'), $rendered);
    }

    /**
     * @test
     */
    public function email_has_a_subject()
    {

        $order = factory(Order::class)->make();
        $email = new OrderConfirmationEmail($order);
        $this->assertEquals("Your TicketBeast Order", $email->build()->subject);
    }
}
