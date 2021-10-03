<?php

namespace Fastwf\Twig\Extension;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

use Fastwf\Core\Router\RouterService;

class FrameworkExtension extends AbstractExtension
{

    private $context;

    /**
     * Constructor
     *
     * @param Fastwf\Core\Engine\Context $context the engine context
     */
    public function __construct($context)
    {
        $this->context = $context;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction(
                'url_for',
                [$this->context->getService(RouterService::class), 'urlFor'],
            ),
        ];
    }

}
