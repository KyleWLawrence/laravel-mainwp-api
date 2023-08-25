<?php

namespace KyleWLawrence\MainWP\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \MainWP\Api\HttpClient
 */
class MainWP extends Facade
{
    /**
     * Return facade accessor.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'MainWP';
    }
}
