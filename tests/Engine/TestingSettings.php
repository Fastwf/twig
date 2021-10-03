<?php

namespace Fastwf\Tests\Engine;

use Fastwf\Core\Settings\RouteSettings;

class TestingSettings implements RouteSettings
{
    
    private $routes;

    public function __construct($routes)
    {
        $this->routes = $routes;
    }

    public function getRoutes($engine)
    {
        return $this->routes;
    }

}
