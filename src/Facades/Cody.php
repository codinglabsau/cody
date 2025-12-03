<?php

namespace Codinglabs\Cody\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Codinglabs\Cody\Cody
 */
class Cody extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Codinglabs\Cody\Cody::class;
    }
}
