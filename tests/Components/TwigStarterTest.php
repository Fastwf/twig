<?php

namespace Fastwf\Twig\Components;

use PHPUnit\Framework\TestCase;
use Fastwf\Core\Session\Session;
use Fastwf\Tests\Engine\TestingEngine;
use Fastwf\Core\Http\Frame\HttpRequest;
use Fastwf\Core\Session\SessionService;
use Fastwf\Core\Session\PhpSessionManager;

use Fastwf\Twig\TwigService;
use Fastwf\Twig\Components\TwigStarter;

class TwigStarterTest extends TestCase
{

    /**
     * @covers Fastwf\Twig\TwigService
     * @covers Fastwf\Twig\Components\TwigStarter
     * @covers Fastwf\Twig\Extension\FrameworkExtension
     * @covers Fastwf\Twig\Extension\FormExtension
     * @covers Fastwf\Twig\Extension\RouteModel
     */
    public function testStart()
    {
        $inputInterceptor = new TwigStarter();

        /** @var TestingEngine */
        $engine = $this->getMockBuilder(TestingEngine::class)
            ->setConstructorArgs([__DIR__."/../../resources/configuration.ini"])
            ->onlyMethods(['handleRequest', 'sendResponse'])
            ->getMock();
        $engine->run();

        // Mock the session service to prevent to call session_start()
        $sessionArray = [];
        $sessionService = $this->getMockBuilder(PhpSessionManager::class)
            ->setConstructorArgs([$engine])
            ->getMock();
        $sessionService->method('getSession')
            ->willReturn(new Session($sessionArray));
        $engine->registerService(SessionService::class, $sessionService);

        $request = new HttpRequest("/login", "POST");
        $request->name = "request_name";
        $inputInterceptor->start($engine, $request);

        $this->assertEquals(
            "The method is request_name with POST",
            $engine->getService(TwigService::class)->renderTemplateString(
                "The method is {{ request.name }} with {{ request.method }}"
            )
        );
    }

}
