<?php

namespace App\Facades;

use App\TicketCodeGenerator;   //interface
use Illuminate\Support\Facades\Facade;

class TicketCode extends Facade
{
    protected static function getFacadeAccessor()
    {
       return TicketCodeGenerator::class;
    }

    //este metodo es util cuando estoy trabajando en un test que todavia no tiene la clase definida para instanciar
    public static function getMockableClass()
    {
        return static::getFacadeAccessor();
    }

}

