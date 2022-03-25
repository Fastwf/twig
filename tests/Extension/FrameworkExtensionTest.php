<?php

namespace Fastwf\Tests\Extension;

use Fastwf\Twig\TwigService;
use Fastwf\Core\Router\Route;
use PHPUnit\Framework\TestCase;

use Fastwf\Tests\Engine\TestingEngine;
use Fastwf\Tests\Engine\TestingSettings;

class FrameworkExtensionTest extends TestCase
{

    /**
     * @covers Fastwf\Twig\TwigService
     * @covers Fastwf\Twig\Extension\FrameworkExtension
     * @covers Fastwf\Twig\Extension\FormExtension
     * @covers Fastwf\Twig\Extension\RouteModel
     */
    public function testRenderUrlFor() {
        /** @var TestingEngine */
        $engine = $this->getMockBuilder(TestingEngine::class)
            ->setConstructorArgs([
                __DIR__ . '/../../resources/configuration.ini',
                [
                    new TestingSettings([
                        new Route([
                            'path' => 'user/{int:id}',
                            'methods' => ['GET'],
                            'handler' => null,
                            'name' => 'getUserInfo', 
                        ]),
                    ]),
                ]
            ])
            ->onlyMethods(['handleRequest', 'sendResponse'])
            ->getMock();
        $engine->run();

        $service = new TwigService($engine);

        $this->assertEquals(
            'http://localhost/user/15',
            $service->renderTemplateString(
                'http://localhost{{ url_for(routeName, routeParameters) }}',
                [
                    "routeName" => "getUserInfo",
                    "routeParameters" => [
                        "getUserInfo/id" => 15
                    ],
                ],
            ),
        );
    }

    /**
     * @covers Fastwf\Twig\TwigService
     * @covers Fastwf\Twig\Extension\FrameworkExtension
     * @covers Fastwf\Twig\Extension\FormExtension
     * @covers Fastwf\Twig\Extension\RouteModel
     */
    public function testRenderAsset()
    {
        /** @var TestingEngine */
        $engine = $this->getMockBuilder(TestingEngine::class)
            ->setConstructorArgs([__DIR__ . '/../../resources/configuration.ini'])
            ->onlyMethods(['handleRequest', 'sendResponse'])
            ->getMock();
        $engine->run();

        $service = $engine->getService(TwigService::class);

        $this->assertEquals(
            '/static/css/style.css',
            $service->renderTemplateString('{{ asset(style) }}', ['style' => '/static/css/style.css'])
        );
    }

    /**
     * @covers Fastwf\Twig\TwigService
     * @covers Fastwf\Twig\Extension\FrameworkExtension
     * @covers Fastwf\Twig\Extension\FormExtension
     * @covers Fastwf\Twig\Extension\RouteModel
     */
    public function testRenderAssetUsingBaseUrl()
    {
        /** @var TestingEngine */
        $engine = $this->getMockBuilder(TestingEngine::class)
            ->setConstructorArgs([__DIR__ . '/alt-config.ini'])
            ->onlyMethods(['handleRequest', 'sendResponse'])
            ->getMock();
        $engine->run();

        $service = $engine->getService(TwigService::class);

        $this->assertEquals(
            '/base-url/static/css/style.css',
            $service->renderTemplateString('{{ asset(style) }}', ['style' => '/static/css/style.css'])
        );
    }

}
