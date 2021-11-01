<?php

namespace pdik\signhost;

use Illuminate\Support\Facades\Facade;

class SignhostFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'signhost';
    }
}