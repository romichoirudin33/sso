<?php

namespace Romichoirudin33\Sso\Facades;

use Illuminate\Support\Facades\Facade;
use Romichoirudin33\Sso\Contracts\Factory;

class Sso extends Facade
{

    protected static function getFacadeAccessor()
    {
        return Factory::class;
    }

}
