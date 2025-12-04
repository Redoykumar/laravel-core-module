<?php
namespace Redoy\CoreModule\Facades;

use Illuminate\Support\Facades\Facade;

class CoreResponse extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'core-response';
    }
}
