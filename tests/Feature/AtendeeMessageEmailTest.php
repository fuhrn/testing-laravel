<?php

namespace Tests\Feature;

use App\AttendeeMessage;
use App\Mail\AttendeeMessageEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class AttendeeMessageEmailTest extends TestCase
{
    /** @test
     * @group
     */
    function email_has_the_correct_subject_and_message()
    {
        $message = new AttendeeMessage([
            'subject' => 'My subject',
            'message' => 'My message',
        ]);
        $email = new AttendeeMessageEmail($message);

        $this->assertEquals("My subject", $email->build()->subject);
        $this->assertEquals("My message", trim($this->render($email)));
    }

    private function render($mailable)
    {
        $mailable->build();
        return view($mailable->textView, $mailable->buildViewData())->render();
    }
}
