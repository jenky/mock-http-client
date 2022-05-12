<?php

namespace VendorName\Skeleton\Facades;

use Illuminate\Support\Facades\Facade;

class Skeleton extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        return 'skeleton';
    }
}
