<?php

namespace Fastwf\Twig;

use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Loader\ChainLoader;
use Fastwf\Core\Engine\Service;
use Twig\Loader\FilesystemLoader;
use Fastwf\Api\Utils\AsyncProperty;
use Fastwf\Twig\Extension\FormExtension;
use Fastwf\Twig\Extension\FrameworkExtension;

/**
 * Service class that allows to render templates using Twig engine.
 */
class TwigService extends Service {

    /**
     * The file system template loader as async property.
     *
     * @var AsyncProperty<FilesystemLoader>
     */
    private $loader;

    /**
     * The array template loader as async property.
     *
     * @var AsyncProperty<ArrayLoader>
     */
    private $arrayLoader;

    /**
     * The environment as async property.
     *
     * @var AsyncProperty<Environment>
     */
    private $environment;

    /**
     * The twig main extension.
     *
     * @var FrameworkExtension
     */
    private $extension;

    public function __construct($context) {
        parent::__construct($context);

        $this->extension = new FrameworkExtension($context);

        $this->arrayLoader = new AsyncProperty(function () { return new ArrayLoader([]); });
        $this->loader = new AsyncProperty(function () use ($context) {
            // Create the template loader asynchronously to use it only when it's required
            //
            // Load all template path from 'twig.templatePath' configuration as an array or a basic string
            $templates = $context->getConfiguration()->get(TwigConfiguration::TEMPLATE_PATHS, 'templates');
            if (\gettype($templates) === 'string')
            {
                // Correct the template to have an array
                $templates = [$templates];
            }

            $fsLoader = new FilesystemLoader([], $context->getRootPath());

            // Add fastwf default templates
            //  Add default form theme
            $fsLoader->addPath(__DIR__.'/../form/', 'fastwf');

            // Analyse each path to detect a namespace and add them to the fs loader
            foreach ($templates as $template)
            {
                $index = strpos($template, PATH_SEPARATOR);
                if ($index !== false)
                {
                    $namespace = substr($template, 0, $index);
                    $path = substr($template, $index + 1);
                }
                else
                {
                    $namespace = FilesystemLoader::MAIN_NAMESPACE;
                    $path = $template;
                }

                $fsLoader->addPath($path, $namespace);
            }

            return $fsLoader;
        });
        $this->environment = new AsyncProperty(function () use ($context) {
            $modeProduction = $context->getConfiguration()->getBoolean('server.modeProduction', false);

            $environment = new Environment(
                new ChainLoader([
                    $this->loader->get(),
                    $this->arrayLoader->get(),
                ]),
                [
                    'cache' => $context->getCachePath('twig.twig'),
                    'auto_reload' => !$modeProduction,
                    'strict_variables' => true,
                ]
            );
            $environment->addExtension($this->extension);
            $environment->addExtension(new FormExtension($context, $environment));

            return $environment;
        });
    }

    /**
     * Inject in the global context the key/value pair.
     * 
     * Warning: the environment is loaded when global must be added.
     * Prefer adding global variable to context when twig rendering must be used in the request handler.
     *
     * @param string $name the name of the key
     * @param mixed $value the value to save for the given key
     * @return void
     */
    public function addGlobal($name, $value)
    {
        $this->environment->get()
            ->addGlobal($name, $value);
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
        $env = $this->environment->get();

        // The loader interface is an ArrayLoader, override the previous runtime template 
        $runtimeTemplateName = "__runtime";
        $this->arrayLoader->get()
            ->setTemplate($runtimeTemplateName, $template);

        // Render the tempate using the unique runtime template name
        return $env->render($runtimeTemplateName, $context);
    }

}
