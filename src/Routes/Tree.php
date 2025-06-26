<?php

namespace Tualo\Office\Security\Routes;

use Tualo\Office\Basic\TualoApplication;
use Tualo\Office\Basic\Route;
use Tualo\Office\Basic\IRoute;
use Tualo\Office\FiskalyAPI\API;


class Tree implements IRoute
{
    public static function register()
    {
        Route::add('/security/tree', function ($matches) {
            TualoApplication::contenttype('application/json');
            try {
                TualoApplication::result('routes', Route::getRoutes());
            } catch (\Exception $e) {
                TualoApplication::result('msg', $e->getMessage());
            }
        }, ['get'], true);
    }
}
