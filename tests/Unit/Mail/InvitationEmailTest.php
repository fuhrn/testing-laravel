<?php

namespace Tests\Unit\Mail;

use App\Invitation;
use Tests\TestCase;
use App\Mail\InvitationEmail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvitationEmailTest extends TestCase
{
    /** @test
     * @group
     */
    function email_contains_a_link_to_accept_the_invitation()
    {
        $invitation = factory(Invitation::class)->make([
            'email' => 'john@example.com',
            'code' => 'TESTCODE1234',
        ]);

        $email = new InvitationEmail($invitation);

        $this->assertStringContainsString(url('/invitations/TESTCODE1234'), $email->render());
    }
    /** @test
     * @group 1
     */
    function email_has_the_correct_subject()
    {
        $invitation = factory(Invitation::class)->make();

        $email = new InvitationEmail($invitation);

        $this->assertEquals("You're invited to join TicketBeast!", $email->build()->subject);
    }
}
