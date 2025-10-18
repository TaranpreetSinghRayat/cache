<?php

namespace Tweekersnut\FormsLib\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class FormsFacade extends Facade
{
    /**
     * Get the registered name of the component
     */
    protected static function getFacadeAccessor(): string
    {
        return 'forms-lib';
    }
}

