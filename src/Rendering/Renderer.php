<?php

namespace Tweekersnut\FormsLib\Rendering;

use Tweekersnut\FormsLib\Core\FormBuilder;
use Tweekersnut\FormsLib\Fields\Field;

class Renderer
{
    protected string $theme;
    protected array $templates = [];

    public function __construct(string $theme = 'bootstrap')
    {
        $this->theme = $theme;
        $this->loadTemplates();
    }

    /**
     * Load theme templates
     */
    protected function loadTemplates(): void
    {
        $templatePath = __DIR__ . '/Templates/' . $this->theme;
        if (is_dir($templatePath)) {
            foreach (glob($templatePath . '/*.php') as $file) {
                $name = basename($file, '.php');
                $this->templates[$name] = $file;
            }
        }
    }

    /**
     * Render complete form
     */
    public function renderForm(FormBuilder $form): string
    {
        $html = '<form';
        $html .= ' name="' . htmlspecialchars($form->getName()) . '"';
        $html .= ' method="' . htmlspecialchars($form->getMethod()) . '"';
        if ($form->getAction()) {
            $html .= ' action="' . htmlspecialchars($form->getAction()) . '"';
        }
        $html .= $this->renderAttributes($form->getAttributes());
        $html .= ' data-form-theme="' . $this->theme . '"';
        $html .= '>';

        // Add CSRF token if enabled
        if ($form->hasCsrfToken()) {
            $html .= $this->renderCsrfToken($form);
        }

        foreach ($form->getFields() as $field) {
            $html .= $this->renderField($field, $form->getError($field->getName()));
        }

        $html .= '</form>';
        return $html;
    }

    /**
     * Render CSRF token hidden field
     */
    protected function renderCsrfToken(FormBuilder $form): string
    {
        $tokenName = htmlspecialchars($form->getCsrfTokenName());
        $tokenValue = htmlspecialchars($form->getCsrfTokenValue() ?? '');
        return '<input type="hidden" name="' . $tokenName . '" value="' . $tokenValue . '">';
    }

    /**
     * Render a single field with wrapper
     */
    public function renderField(Field $field, ?string $error = null): string
    {
        $template = $this->getTemplate('field');
        if (!$template) {
            return $this->renderFieldFallback($field, $error);
        }

        ob_start();
        include $template;
        return ob_get_clean();
    }

    /**
     * Render field input only
     */
    public function renderFieldInput(Field $field): string
    {
        $template = $this->getTemplate('input-' . $field->getType());
        if (!$template) {
            $template = $this->getTemplate('input-text');
        }

        if (!$template) {
            return $this->renderFieldInputFallback($field);
        }

        ob_start();
        include $template;
        return ob_get_clean();
    }

    /**
     * Render field label
     */
    public function renderLabel(Field $field): string
    {
        $template = $this->getTemplate('label');
        if (!$template) {
            return $this->renderLabelFallback($field);
        }

        ob_start();
        include $template;
        return ob_get_clean();
    }

    /**
     * Render field error
     */
    public function renderError(Field $field, ?string $error): string
    {
        if (!$error) {
            return '';
        }

        $template = $this->getTemplate('error');
        if (!$template) {
            return '<div class="error">' . htmlspecialchars($error) . '</div>';
        }

        ob_start();
        include $template;
        return ob_get_clean();
    }

    /**
     * Render field help text
     */
    public function renderHelp(Field $field): string
    {
        if (!$field->getHelpText()) {
            return '';
        }

        $template = $this->getTemplate('help');
        if (!$template) {
            return '<small>' . htmlspecialchars($field->getHelpText()) . '</small>';
        }

        ob_start();
        include $template;
        return ob_get_clean();
    }

    /**
     * Get template file path
     */
    protected function getTemplate(string $name): ?string
    {
        return $this->templates[$name] ?? null;
    }

    /**
     * Render HTML attributes
     */
    protected function renderAttributes(array $attributes): string
    {
        $html = '';
        foreach ($attributes as $key => $value) {
            if ($value === true) {
                $html .= ' ' . htmlspecialchars($key);
            } elseif ($value !== false && $value !== null) {
                $html .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
            }
        }
        return $html;
    }

    /**
     * Fallback rendering methods
     */
    protected function renderFieldFallback(Field $field, ?string $error): string
    {
        return '<div class="form-group">' .
            $this->renderLabelFallback($field) .
            $this->renderFieldInputFallback($field) .
            $this->renderError($field, $error) .
            '</div>';
    }

    protected function renderFieldInputFallback(Field $field): string
    {
        $type = $field->getType();
        $name = htmlspecialchars($field->getName());
        $value = htmlspecialchars($field->getValue() ?? '');
        $attrs = $this->renderAttributes($field->getAttributes());

        return match ($type) {
            'textarea' => "<textarea name=\"$name\"$attrs>$value</textarea>",
            'select' => $this->renderSelectFallback($field),
            'checkbox' => $this->renderCheckboxFallback($field),
            'radio' => $this->renderRadioFallback($field),
            'file' => "<input type=\"file\" name=\"$name\"$attrs>",
            'hidden' => "<input type=\"hidden\" name=\"$name\" value=\"$value\">",
            'submit' => "<button type=\"submit\" name=\"$name\"$attrs>$value</button>",
            'reset' => "<button type=\"reset\" name=\"$name\"$attrs>$value</button>",
            'button' => "<button type=\"button\" name=\"$name\"$attrs>$value</button>",
            default => "<input type=\"text\" name=\"$name\" value=\"$value\"$attrs>",
        };
    }

    protected function renderLabelFallback(Field $field): string
    {
        $name = htmlspecialchars($field->getName());
        $label = htmlspecialchars($field->getLabel());
        $required = $field->isRequired() ? ' <span class="required">*</span>' : '';
        return "<label for=\"$name\">$label$required</label>";
    }

    protected function renderSelectFallback(Field $field): string
    {
        $name = htmlspecialchars($field->getName());
        $value = $field->getValue();
        $attrs = $this->renderAttributes($field->getAttributes());
        $html = "<select name=\"$name\"$attrs>";

        foreach ($field->getOptions() as $optValue => $optLabel) {
            $selected = $value == $optValue ? ' selected' : '';
            $html .= "<option value=\"" . htmlspecialchars($optValue) . "\"$selected>" .
                htmlspecialchars($optLabel) . "</option>";
        }

        $html .= "</select>";
        return $html;
    }

    protected function renderCheckboxFallback(Field $field): string
    {
        $name = htmlspecialchars($field->getName());
        $value = $field->getValue();
        $html = '';

        foreach ($field->getOptions() as $optValue => $optLabel) {
            $checked = $value == $optValue ? ' checked' : '';
            $html .= "<label><input type=\"checkbox\" name=\"$name\" value=\"" .
                htmlspecialchars($optValue) . "\"$checked> " .
                htmlspecialchars($optLabel) . "</label>";
        }

        return $html;
    }

    protected function renderRadioFallback(Field $field): string
    {
        $name = htmlspecialchars($field->getName());
        $value = $field->getValue();
        $html = '';

        foreach ($field->getOptions() as $optValue => $optLabel) {
            $checked = $value == $optValue ? ' checked' : '';
            $html .= "<label><input type=\"radio\" name=\"$name\" value=\"" .
                htmlspecialchars($optValue) . "\"$checked> " .
                htmlspecialchars($optLabel) . "</label>";
        }

        return $html;
    }
}

