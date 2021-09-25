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
     */
    public function testRender() {
        $engine = new TestingEngine(__DIR__ . '/../resources/configuration.ini');

        $service = new TwigService($engine);

        $this->assertEquals(
            \file_get_contents(__DIR__ . '/../resources/expected/index.html'),
            $service->render('index.html', ['engine' => 'Twig']),
        );
    }

    /**
     * @covers Fastwf\Twig\TwigService
     */
    public function testPrependPath() {
        $engine = new TestingEngine(__DIR__ . '/../resources/configuration.ini');

        $service = new TwigService($engine);
        $service->prependPath(__DIR__ . '/../resources/prepend-templates');

        $this->assertEquals(
            \file_get_contents(__DIR__ . '/../resources/expected/index-prepend.html'),
            $service->render('index.html', ['engine' => 'Twig']),
        );
    }

    /**
     * @covers Fastwf\Twig\TwigService
     */
    public function testAddPath() {
        $engine = new TestingEngine(__DIR__ . '/../resources/configuration.ini');

        $service = new TwigService($engine);
        $service->addPath(__DIR__ . '/../resources/templates');

        $this->assertEquals(
            \file_get_contents(__DIR__ . '/../resources/expected/home.html'),
            $service->render('home.html', ['engine' => 'Twig']),
        );
    }

    /**
     * @covers Fastwf\Twig\TwigService
     */
    public function testSetPaths() {
        $engine = new TestingEngine(__DIR__ . '/../resources/configuration.ini');

        $service = new TwigService($engine);
        $service->setPaths([__DIR__ . '/../resources/prepend-templates']);

        $this->assertEquals(
            \file_get_contents(__DIR__ . '/../resources/expected/index-prepend.html'),
            $service->render('index.html', ['engine' => 'Twig']),
        );
    }

}
