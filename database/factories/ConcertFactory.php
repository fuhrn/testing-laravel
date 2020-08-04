<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Concert;
use Carbon\Carbon;
use Faker\Generator as Faker;
use App\User;

$factory->define(Concert::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'title' => 'Example band',
        'subtitle' => 'with The Fake Openers',
        'additional_information' => 'Some sample additional information.',
        'date' => Carbon::parse('+2 weeks'),
        'venue' => 'The Example Theatre',
        'venue_address' => '123 Example Lane',
        'city' => 'Fakeville',
        'state' => 'ON',
        'zip' => '90210',
        'ticket_price' => 2000,
        'ticket_quantity' => 5,
    ];
});

$factory->state(Concert::class, 'published', function ($faker) {
    return [
        'published_at' => Carbon::parse('-1 week'),
    ];
});

$factory->state(Concert::class, 'unpublished', function ($faker) {
    return [
        'published_at' => null,
    ];
});
