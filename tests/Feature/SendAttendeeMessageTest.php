<?php

namespace Tests\Feature;

use App\Mail\AttendeeMessageEmail;
use OrderFactoryHelper;
use ConcertFactoryHelper;
use Tests\TestCase;
use App\AttendeeMessage;
use App\Jobs\SendAttendeeMessage;
//use App\Mail\AttendeeMessageEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SendAttendeeMessageTest extends TestCase
{
    use RefreshDatabase;

    /** @test
     * @group
     */
    function it_sends_the_message_to_all_concert_attendees()
    {
        Mail::fake();
        $concert = ConcertFactoryHelper::createPublished();
        $otherConcert = ConcertFactoryHelper::createPublished();
        $message = AttendeeMessage::create([
            'concert_id' => $concert->id,
            'subject' => 'My subject',
            'message' => 'My message',
        ]);
        $orderA = OrderFactoryHelper::createForConcert($concert, ['email' => 'alex@example.com']);
        $otherOrder = OrderFactoryHelper::createForConcert($otherConcert, ['email' => 'jane@example.com']);
        $orderB = OrderFactoryHelper::createForConcert($concert, ['email' => 'sam@example.com']);
        $orderC = OrderFactoryHelper::createForConcert($concert, ['email' => 'taylor@example.com']);

        SendAttendeeMessage::dispatch($message);

        Mail::assertQueued(AttendeeMessageEmail::class, function ($mail) use ($message) {
            return $mail->hasTo('alex@example.com')
                && $mail->attendeeMessage->is($message);
        });
        Mail::assertQueued(AttendeeMessageEmail::class, function ($mail) use ($message) {
            return $mail->hasTo('sam@example.com')
                && $mail->attendeeMessage->is($message);
        });
        Mail::assertQueued(AttendeeMessageEmail::class, function ($mail) use ($message) {
            return $mail->hasTo('taylor@example.com')
                && $mail->attendeeMessage->is($message);
        });
        Mail::assertNotQueued(AttendeeMessageEmail::class, function ($mail) {
            return $mail->hasTo('jane@example.com');
        });
    }
}
