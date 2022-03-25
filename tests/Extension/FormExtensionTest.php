<?php

namespace Fastwf\Tests\Extension;

use PHPUnit\Framework\TestCase;
use Fastwf\Tests\Engine\TestingEngine;
use Fastwf\Twig\TwigService;

class FormExtensionTest extends TestCase
{

    private $form;

    protected function setUp(): void
    {
        $this->form = [
            "name" => null,
            "attributes" => [],
            "action" => "",
            "method" => "post",
            "enctype" => "application/x-www-form-urlencoded",
            "containerType" => "object",
            "controls" => [
                [
                    "name" => "comment",
                    "fullName" => "comment",
                    "label" => "Comment",
                    "help" => "Write a comment about this article.",
                    "tag" => "textarea",
                    "attributes" => [
                        "id" => "textarea-comment",
                        "required" => true
                    ],
                    "value" => null,
                    "svalue" => "",
                    "violation" => [
                        "violations" => [
                            ["message" => "This field is required"]
                        ]
                    ],
                ],
                [
                    "name" => null,
                    "attributes" => [],
                    "tag" => "input",
                    "type" => "submit",
                    "value" => "Publish",
                    "svalue" => "Publish",
                    "violation" => null,
                ]
            ]
        ];
    }

    /**
     * Generate a setup TestingEngine.
     *
     * @param string $configurationPath
     * @return TestingEngine
     */
    private function getTestingEngine($configurationPath) {
        /** @var TestingEngine */
        $engine = $this->getMockBuilder(TestingEngine::class)
            ->setConstructorArgs([$configurationPath])
            ->onlyMethods(['handleRequest', 'sendResponse'])
            ->getMock();
        $engine->run();

        return $engine;
    }

    /**
     * @covers Fastwf\Twig\TwigService
     * @covers Fastwf\Twig\Extension\FrameworkExtension
     * @covers Fastwf\Twig\Extension\FormExtension
     * @covers Fastwf\Twig\Extension\RouteModel
     */
    public function testRenderForm()
    {
        $engine = $this->getTestingEngine(__DIR__ . '/../../resources/configuration.ini');

        /** @var TwigService */
        $service = $engine->getService(TwigService::class);

        $this->assertEquals(
            file_get_contents(__DIR__ . '/../../resources/expected/form.html'),
            $service->renderTemplateString("{{ form(form) }}", ['form' => $this->form])
        );
    }

    /**
     * @covers Fastwf\Twig\TwigService
     * @covers Fastwf\Twig\Extension\FrameworkExtension
     * @covers Fastwf\Twig\Extension\FormExtension
     * @covers Fastwf\Twig\Extension\RouteModel
     */
    public function testRenderFormExtendedTheme()
    {
        $engine = $this->getTestingEngine(__DIR__ . '/../../resources/configuration.ini');

        /** @var TwigService */
        $service = $engine->getService(TwigService::class);

        $this->assertEquals(
            file_get_contents(__DIR__ . '/../../resources/expected/form-theme.html'),
            $service->render("comment.html.twig", ['form' => $this->form])
        );
    }

}
