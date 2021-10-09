<?php

namespace Fastwf\Tests;

use Fastwf\Twig\TwigService;
use Fastwf\Tests\Engine\TestingEngine;
use PHPUnit\Framework\TestCase;

class TwigServiceTest extends TestCase {

    protected function setUp(): void
    {
        $_SERVER['DOCUMENT_ROOT'] = __DIR__;
    }

    /**
     * @covers Fastwf\Twig\TwigService
     * @covers Fastwf\Twig\Extension\FrameworkExtension
     * @covers Fastwf\Twig\Extension\RouteModel
     */
    public function testRender() {
        $engine = $this->getTestingEngine(__DIR__ . '/../resources/configuration.ini');

        $service = new TwigService($engine);

        $this->assertEquals(
            \file_get_contents(__DIR__ . '/../resources/expected/index.html'),
            $service->render('index.html', ['engine' => 'Twig']),
        );
    }

    /**
     * @covers Fastwf\Twig\TwigService
     * @covers Fastwf\Twig\Extension\FrameworkExtension
     * @covers Fastwf\Twig\Extension\RouteModel
     */
    public function testPrependPath() {
        $engine = $this->getTestingEngine(__DIR__ . '/../resources/configuration.ini');

        $service = new TwigService($engine);
        $service->prependPath(__DIR__ . '/../resources/prepend-templates');

        $this->assertEquals(
            \file_get_contents(__DIR__ . '/../resources/expected/index-prepend.html'),
            $service->render('index.html', ['engine' => 'Twig']),
        );
    }

    /**
     * @covers Fastwf\Twig\TwigService
     * @covers Fastwf\Twig\Extension\FrameworkExtension
     * @covers Fastwf\Twig\Extension\RouteModel
     */
    public function testAddPath() {
        $engine = $this->getTestingEngine(__DIR__ . '/../resources/configuration.ini');

        $service = new TwigService($engine);
        $service->addPath(__DIR__ . '/../resources/templates');

        $this->assertEquals(
            \file_get_contents(__DIR__ . '/../resources/expected/home.html'),
            $service->render('home.html', ['engine' => 'Twig']),
        );
    }

    /**
     * @covers Fastwf\Twig\TwigService
     * @covers Fastwf\Twig\Extension\FrameworkExtension
     * @covers Fastwf\Twig\Extension\RouteModel
     */
    public function testSetPaths() {
        $engine = $this->getTestingEngine(__DIR__ . '/../resources/configuration.ini');

        $service = new TwigService($engine);
        $service->setPaths([__DIR__ . '/../resources/prepend-templates']);

        $this->assertEquals(
            \file_get_contents(__DIR__ . '/../resources/expected/index-prepend.html'),
            $service->render('index.html', ['engine' => 'Twig']),
        );
    }

    /**
     * @covers Fastwf\Twig\TwigService
     * @covers Fastwf\Twig\Extension\FrameworkExtension
     * @covers Fastwf\Twig\Extension\RouteModel
     */
    public function testRenderTemplateString() {
        $engine = $this->getTestingEngine(__DIR__ . '/../resources/configuration.ini');

        $service = new TwigService($engine);

        $this->assertEquals(
            'Hello Foo!',
            $service->renderTemplateString(
                'Hello {{ name|capitalize }}!',
                ['name' => 'foo'],
            ),
        );
    }

    /**
     * Generate a setup TestingEngine.
     *
     * @param string $configurationPath
     * @return Fastwf\Tests\Engine\TestingEngine
     */
    private function getTestingEngine($configurationPath) {
        $engine = $this->getMockBuilder(TestingEngine::class)
            ->setConstructorArgs([$configurationPath])
            ->onlyMethods(['handleRequest', 'sendResponse'])
            ->getMock();
        $engine->run();

        return $engine;
    }

}
