<?php

namespace Tualo\Office\Security\Routes;

use Tualo\Office\Basic\TualoApplication;
use Tualo\Office\Basic\Route;
use Tualo\Office\Basic\IRoute;
use Tualo\Office\FiskalyAPI\API;

use Tualo\Office\Basic\Session;
use Tualo\Office\Basic\MYSQL\Database;

class Tree extends \Tualo\Office\Basic\RouteWrapper
{
    public static function scope(): string
    {
        return 'security.tree';
    }
    public static function register()
    {
        Route::add('/security/tree', function ($matches) {
            TualoApplication::contenttype('application/json');
            try {
                $rx = [];
                $routes = Route::getRoutes();
                foreach ($routes as $route) {
                    if (isset($route['needActiveSession']) && ($route['needActiveSession'] === false)) {
                        $rx[] = $route;
                    }
                }


                TualoApplication::result('routes', $rx);
            } catch (\Exception $e) {
                TualoApplication::result('msg', $e->getMessage());
            }
        }, ['get'], true, [], self::scope());
    }
}
