<?php

namespace Fastwf\Twig;

/**
 * Utility class that define configuration keys names.
 */
class TwigConfiguration
{

    /**
     * The configuration key to use to load templates paths.
     * 
     * This key must define a string (`twig.templatePaths = path/to/templates`) or an array (`twig.templatePaths[] = path/to/template1`) of
     * relative path (to root server) or absolute path. In that case, directories are registered in main twig namespace.
     * 
     * A template path with namespace must respect the next syntax:
     * - Windows: `twig.templatePaths[] = "namespace;path/to/templateDir"`
     * - linux base OS: `twig.templatePaths[] = namespace:path/to/templateDir`
     * 
     * > The namespace must use the path separator according to the OS. For windows, we use `;` but it's a ini comment, so use `;` inside
     * string delimiters. 
     */
    public const TEMPLATE_PATHS = 'twig.templatePaths'; 

    /**
     * The configuration key to use to use a default form theme.
     * 
     * This key must define a string that respect the template naming convention (path or path with namespace).
     */
    public const DEFAULT_FORM_THEME = 'twig.defaultFormTheme';

}
