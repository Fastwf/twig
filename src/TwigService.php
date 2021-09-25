<?php

namespace Fastwf\Twig;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Fastwf\Core\Engine\Service;
use Fastwf\Core\Utils\AsyncProperty;

/**
 * Service class that allows to render templates using Twig engine.
 */
class TwigService extends Service {

    private $loader;
    private $environment;

    public function __construct($context) {
        parent::__construct($context);

        $this->loader = new AsyncProperty(function () use ($context) {
            // Create the template loader asynchronously to use it only when it's required
            return new FilesystemLoader(
                [$context->getConfiguration()->get('twig.templatePath', 'templates')],
                $context->getRootPath()
            );
        });
        $this->environment = new AsyncProperty(function () use ($context) {
            $modeProduction = $context->getConfiguration()->getBoolean('server.modeProduction', false);

            return new Environment(
                $this->loader->get(),
                [
                    'cache' => $context->getCachePath('twig.twig'),
                    'auto_reload' => !$modeProduction,
                    'strict_variables' => true,
                ]
            );
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
     * Undocumented function
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

}
