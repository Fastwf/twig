<?php

namespace Fastwf\Twig;

use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Loader\FilesystemLoader;
use Fastwf\Core\Engine\Service;
use Fastwf\Core\Utils\AsyncProperty;

use Fastwf\Twig\Extension\FrameworkExtension;

/**
 * Service class that allows to render templates using Twig engine.
 */
class TwigService extends Service {

    private $loader;
    private $environment;
    private $runtimeEnvironment;
    private $extension; 

    public function __construct($context) {
        parent::__construct($context);

        $this->extension = new FrameworkExtension($context);

        $this->loader = new AsyncProperty(function () use ($context) {
            // Create the template loader asynchronously to use it only when it's required
            return new FilesystemLoader(
                [$context->getConfiguration()->get('twig.templatePath', 'templates')],
                $context->getRootPath()
            );
        });
        $this->environment = new AsyncProperty(function () use ($context) {
            $modeProduction = $context->getConfiguration()->getBoolean('server.modeProduction', false);

            $environment = new Environment(
                $this->loader->get(),
                [
                    'cache' => $context->getCachePath('twig.twig'),
                    'auto_reload' => !$modeProduction,
                    'strict_variables' => true,
                ]
            );
            $environment->addExtension($this->extension);

            return $environment;
        });
        $this->runtimeEnvironment = new AsyncProperty(function () {
            $environment = new Environment(
                new ArrayLoader([]),
                ['strict_variables' => true]
            );
            $environment->addExtension($this->extension);

            return $environment;
        });
    }

    /**
     * Add the root path of templates files at the end of array of loaded root template directories.
     *
     * @param string $path the path of the directory containing templates.
     * @param string $namespace the namespace where store the templates
     */
    public function addPath($path, $namespace = FilesystemLoader::MAIN_NAMESPACE) {
        $this->loader->get()->addPath($path, $namespace);
    }

    /**
     * Add the root path of templates files at the beggining of array of loaded root template directories.
     *
     * @param string $path the path of the directory containing templates.
     * @param string $namespace the namespace where store the templates
     */
    public function prependPath($path, $namespace = FilesystemLoader::MAIN_NAMESPACE) {
        $this->loader->get()->prependPath($path, $namespace);
    }

    /**
     * @param string|array $paths A path or an array of paths where to look for templates
     * @param string $namespace the template namespace
     */
    public function setPaths($paths, $namespace = FilesystemLoader::MAIN_NAMESPACE) {
        $this->loader->get()->setPaths($paths, $namespace);
    }

    /**
     * Render the template specified by name using context.
     *
     * @param string $name The template name.
     * @param string $context The context containing variables to inject in the template.
     * @return string the template rendered with context variables
     */
    public function render($name, $context = []) {
        return $this->environment
            ->get()
            ->render($name, $context);
    }

    /**
     * Render the template from the given template string.
     * 
     * The template don't use the engine twig environment, including templates or extending from an existing template is not possible.
     *
     * @param string $template The string template to render
     * @param array $context The context containing variables to inject in the template.
     * @return string the template rendered with context variables
     */
    public function renderTemplateString($template, $context = []) {
        $runtimeEnv = $this->runtimeEnvironment->get();

        // The loader interface is an ArrayLoader, override the previous runtime template 
        $runtimeTemplateName = "__runtime";
        $runtimeEnv->getLoader()->setTemplate($runtimeTemplateName, $template);

        // Render the tempate using the unique runtime template name
        return $runtimeEnv->render($runtimeTemplateName, $context);
    }

}
