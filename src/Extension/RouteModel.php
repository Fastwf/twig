<?php

namespace Fastwf\Twig\Extension;

use Fastwf\Core\Router\Formatter\IBaseRoute;

/**
 * Route model to use for PartialPathFormatter.
 */
class RouteModel implements IBaseRoute
{
    
    private $path;
    private $name;

    public function __construct($path, $name = null)
    {
        $this->path = $path;
        $this->name = $name;
    }

    /**
     * Get the name of the route when it's set
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the path associated to this route.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

}
