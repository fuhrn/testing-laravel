<?php

namespace App\Facades;

use App\OrderConfirmationNumberGenerator;   //interface
use Illuminate\Support\Facades\Facade;

class OrderConfirmationNumber extends Facade
{
    protected static function getFacadeAccessor()
    {
       return OrderConfirmationNumberGenerator::class;
    }
}

