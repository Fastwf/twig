<?php

namespace Fastwf\Twig\Extension;

use Twig\TwigFunction;
use Fastwf\Core\Engine\Context;
use Fastwf\Api\Utils\StringUtil;
use Fastwf\Core\Router\RouterService;
use Twig\Extension\AbstractExtension;
use Fastwf\Core\Router\Formatter\PathFormatter;
use Fastwf\Core\Router\Formatter\PartialPathFormatter;

class FrameworkExtension extends AbstractExtension
{

    /**
     * The application context.
     *
     * @var Context
     */
    private $context;

    /**
     * The path formatter that allows to generate static asset path.
     *
     * @var PathFormatter
     */
    private $assetFormatter;

    /**
     * Constructor
     *
     * @param Context $context the engine context
     */
    public function __construct($context)
    {
        $this->context = $context;

        $this->assetFormatter = new PathFormatter([
            new PartialPathFormatter(new RouteModel($this->context->getConfiguration()->get('server.baseUrl', ''))),
            new PartialPathFormatter(new RouteModel('{path:path}')),
        ]);
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('asset', [$this, 'getAssetUrl']),
            new TwigFunction('url_for', [$this, 'urlFor']),
        ];
    }

    /**
     * Generate the url path to the static file from the server.baseUrl
     *
     * @param string $path the path to the static asset
     * @return string the path formatted using $path
     */
    public function getAssetUrl($path)
    {
        // Trim the leading '/' to prevent '//'
        if (StringUtil::startsWith($path, '/'))
        {
            $path = \substr($path, 1, \strlen($path));
        }

        // Return the path including the '/' because base url cannot start with '/'
        return $this->assetFormatter->format(['path' => $path]); 
    }

    /**
     * Call RouterService->urlFor method
     *
     * @param string $name the name of the route.
     * @param string $parameters the parameters to inject as path parameter
     * @param string $query the query parameter to add to the url.
     * @param string $fragment the fragment to set to the url.
     * @return sring the path url encoded
     */
    public function urlFor($name, $parameters = null, $query = null, $fragment = null)
    {
        return $this->context->getService(RouterService::class)->urlFor($name, $parameters, $query, $fragment);
    }

}
