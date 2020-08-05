<?php

use App\Concert;

class ConcertFactoryHelper
{
    public static function createPublished($overrides = [])
    {
        $concert = factory(Concert::class)->create($overrides);
        $concert->publish();
        return $concert;
    }
}
