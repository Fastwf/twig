<?php

namespace Fastwf\Twig\Extension;

use Twig\Template;
use Twig\Environment;
use Twig\TwigFunction;
use Fastwf\Core\Engine\Context;
use Fastwf\Twig\TwigConfiguration;
use Twig\Extension\AbstractExtension;

/**
 * The extension that allows to render a from.
 * 
 * This extension allows to render any type or object that respect the next form specifications:
 * 
 * ___
 * 
 * **Basic types**:
 * 
 * **Control**
 * - `name`[`?string`]: The name of the control.
 * - `fullName`[`?string`]: The name containing parent path (`'form[group][control]'`)
 * - `attributes`[`array<string,string|array<string>>`]: The array of HTML attributes to use for widget HTML entity.
 * - `label`[`?string`]: The label of the form control.
 * - `help`[`?string`]: The help message associated to the control.
 * - `tag`[`string|null`]: The html tag for widget and `null` for group.
 * 
 * > **attributes**: values can be a string or an array of string. For `'class'` key, `'form-control mb-3'` and
 * `['form-control', 'mb-3']` are the same.
 * 
 * **ViolationConstraint**
 * - `code`[`string`]: An unique constraint id.
 * - `parameters`[`array<string,mixed>`]: The constraint parameters usable to generate an error message.
 * - `message`[`string`]: The constraint message to display.
 * 
 * **Violation**
 * - `violations`[`array<ViolationConstraint>`]: The array containing violation string.
 * 
 * ___
 * 
 * **Widgets**:
 * 
 * **Form Control**
 * - `value`[`?mixed`]: The original value.
 * - `svalue`[`string`]: The value string representing the form control value or '' if no value is set.
 * - `violation`[`?Violation`]: The violation object that represent the error on the field.
 * 
 * **Button** extends **Form Control**
 * - `type`[`string`]: The button type.
 * 
 * **Input** extends **Form Control**
 * - `type`[`string`]: The button type.
 * 
 * **Textarea** extends **Form Control**
 * 
 * **Select** extends **Form Control**
 * - `options`[`array<AOption>`]: The array of options hold by select.
 * 
 * **AOption**
 * - `label`[`?string`]: The option label.
 * - `disabled`[`boolean`]: a boolean that indicate if the option is disabled.
 * 
 * **Option** extends **AOption**
 * - `selected`[`boolean`]: a boolean that indicate if the option is selected.
 * - `value`[`string`]: The value attached to the select.
 * 
 * **OptionGroup** extends **AOption**
 * - `options`[`array<Option>`]: The array of options hold by option group.
 * 
 * ___
 * 
 * **Groups**:
 * 
 * **Group** extends **Control**
 * - `containerType`[`string`]: the group type (`array`, `object` or `widget`).
 * - `controls`[`array<Control>`]: form control children.
 * 
 * > Group type: `array` for a list of same controls, `object` for different controls and `widget` for multi controls that act as unique
 * form control.
 * 
 * **Form** extends **Group**
 * - `action`[`string`]: the form action url.
 * - `method`[`string`]: the form method when submit data.
 * - `enctype`[`string`]: the encoding of the posted body.
 * 
 * **EntityGroup** extends **Group**
 * - `violation`[`?Violation`]: The violation object that represent the error on the field.
 */
class FormExtension extends AbstractExtension
{

    /**
     * The inventory that hold form themes.
     *
     * @var Template
     */
    protected $themeTemplate;

    /**
     * The current twig environment.
     *
     * @var Environment
     */
    protected $environment;

    /**
     * The default form theme to use.
     *
     * @var string
     */
    protected $defaultTheme;

    /**
     * Constructor.
     *
     * @param Context $context the application engine context.
     * @param Environment $environment the environment.
     */
    public function __construct($context, $environment)
    {
        $this->environment = $environment;
        
        $this->defaultTheme = $context
            ->getConfiguration()
            ->get(TwigConfiguration::DEFAULT_FORM_THEME, '@fastwf/form_theme_default.html.twig');
    }

    public function getFunctions()
    {
        $options = [
            'is_safe' => ['html'],
        ];

        return [
            new TwigFunction('form', [$this, 'form'], $options),
            new TwigFunction('form_start', [$this, 'formStart'], $options),
            new TwigFunction('form_end', [$this, 'formEnd'], $options),
            new TwigFunction('form_row', [$this, 'formRow'], $options),
            new TwigFunction('form_label', [$this, 'formLabel'], $options),
            new TwigFunction('form_control', [$this, 'formControl'], $options),
            new TwigFunction('form_errors', [$this, 'formErrors'], $options),
            new TwigFunction('form_help', [$this, 'formHelp'], $options),
            new TwigFunction('form_default_theme', [$this, 'getDefaultTheme'])
        ];
    }

    /**
     * Load the theme in parameter to render the form.
     * 
     * When the theme is null, the extension load the default from the configuration file.
     * 
     * @param string[] $theme The theme to load.
     * @return void
     */
    private function loadTheme($theme)
    {
        if ($this->themeTemplate === null)
        {
            $this->themeTemplate = $this->environment->load($theme === null ? $this->defaultTheme : $theme);
        }
    }

    /**
     * Render a form using theme in parameter or the default.
     *
     * @param mixed $form the form to render.
     * @param string[] $theme The theme to use to render the form ({@see FormExtension::loadTheme} for details).
     * @return string The form rendered.
     */
    public function form($form, $themes = null)
    {
        $this->loadTheme($themes);

        return $this->themeTemplate->renderBlock('form', ['form' => $form, 'themes' => $themes]);
    }

    /**
     * Render a form start tHTML entity tag using theme in parameter or the default.
     *
     * @param mixed $form the form to render.
     * @param string[] $theme The theme to use to render the form ({@see FormExtension::loadTheme} for details).
     * @return string The form start tag rendered.
     */
    public function formStart($form, $themes = null)
    {
        $this->loadTheme($themes);

        return $this->themeTemplate->renderBlock('form_start', ['form' => $form, 'control' => $form]);
    }

    /**
     * Render the form end HTML entity tag.
     *
     * @param mixed $form
     * @return string The form end tag rendered.
     */
    public function formEnd($form)
    {
        $block = $this->themeTemplate->renderBlock('form_end', ['form' => $form]);

        // Release data about the current loaded theme
        $this->themeTemplate = null;

        return $block;
    }

    /**
     * Render the form control node.
     *
     * @param string $blockName The name of the block to render.
     * @param mixed $control The form control to render.
     * @return string The form control HTML representation.
     */
    private function renderBlockControl($blockName, $control)
    {
        return $this->themeTemplate->renderBlock($blockName, ['control' => $control]);
    }

    /**
     * Render the form row.
     *
     * @param mixed $control The form control to render.
     * @return string The form row HTML representation.
     */
    public function formRow($control)
    {
        return $this->renderBlockControl('form_row', $control);
    }

    /**
     * Render the form label control.
     *
     * @param mixed $control The form control to render.
     * @return string The HTML representation of the control label.
     */
    public function formLabel($control)
    {
        return $this->renderBlockControl('form_label', $control);
    }

    /**
     * Render the form control (the HTML widget like input, textarea, select).
     *
     * @param mixed $control The form control to render.
     * @return string The HTML representation of the control label.
     */
    public function formControl($control)
    {
        return $this->renderBlockControl('form_control', $control);
    }

    /**
     * Render the form control error block.
     *
     * @param mixed $control The form control to render.
     * @return string The HTML representation of form control errors.
     */
    public function formErrors($control)
    {
        return $this->renderBlockControl('form_errors', $control);
    }

    /**
     * Render the form control help block.
     *
     * @param mixed $control The form control to render.
     * @return string The HTML representation of form control help.
     */
    public function formHelp($control)
    {
        return $this->renderBlockControl('form_help', $control);
    }

    /**
     * Get the default theme.
     *
     * @return string
     */
    public function getDefaultTheme()
    {
        return $this->defaultTheme;
    }

}
