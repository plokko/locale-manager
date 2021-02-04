<?php

namespace Plokko\LocaleManager\Facades;

use Illuminate\Support\Facades\Facade;

class LocaleManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Plokko\LocaleManager\LocaleManager::class;
    }
}
