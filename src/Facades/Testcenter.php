<?php

namespace Testcenter\Testcenter\Facades;

use Illuminate\Support\Facades\Facade;

class Testcenter extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'testcenter';
    }
}
