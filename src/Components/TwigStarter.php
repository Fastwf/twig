<?php

namespace Fastwf\Twig\Components;

use Fastwf\Core\Session\SessionService;
use Fastwf\Core\Components\InInterceptor;

use Fastwf\Twig\TwigService;

/**
 * Twig input interceptor that allows to initialize the global context for twig rendering.
 * 
 * When this input interceptor is provided for the request flow, TwigStarter inject in the global context the next variables:
 * - config: the loaded engine configuration.
 * - metadata: the metadata array proxy.
 * - request: the current request object.
 * - session: the non locked session object.
 */
class TwigStarter implements InInterceptor
{

    public function start($context, $request)
    {
        $service = $context->getService(TwigService::class);

        $service->addGlobal('config', $context->getConfiguration());
        $service->addGlobal('metadata', $context->getMetadata());
        $service->addGlobal('request', $request);
        $service->addGlobal('session', $context->getService(SessionService::class)->getSession());

        return $request;
    }

}
