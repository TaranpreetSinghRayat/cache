<?php

namespace Tweekersnut\FormsLib\Core;

use Tweekersnut\FormsLib\Fields\Field;
use Tweekersnut\FormsLib\Rendering\Renderer;
use Tweekersnut\FormsLib\Validation\Validator;

class FormBuilder
{
    protected string $name;
    protected string $method = 'POST';
    protected string $action = '';
    protected array $fields = [];
    protected array $attributes = [];
    protected string $theme = 'bootstrap'; // 'bootstrap' or 'tailwind'
    protected Renderer $renderer;
    protected Validator $validator;
    protected array $values = [];
    protected array $errors = [];
    protected bool $includeCsrfToken = false;
    protected string $csrfTokenName = '_token';
    protected ?string $csrfTokenValue = null;

    public function __construct(string $name, string $theme = 'bootstrap')
    {
        $this->name = $name;
        $this->theme = $theme;
        $this->renderer = new Renderer($theme);
        $this->validator = new Validator();
    }

    /**
     * Set form method (GET, POST, etc.)
     */
    public function method(string $method): self
    {
        $this->method = strtoupper($method);
        return $this;
    }

    /**
     * Set form action URL
     */
    public function action(string $action): self
    {
        $this->action = $action;
        return $this;
    }

    /**
     * Add custom HTML attributes to form
     */
    public function attributes(array $attributes): self
    {
        $this->attributes = array_merge($this->attributes, $attributes);
        return $this;
    }

    /**
     * Add a field to the form
     */
    public function field(Field $field): self
    {
        $this->fields[$field->getName()] = $field;
        return $this;
    }

    /**
     * Set form values
     */
    public function values(array $values): self
    {
        $this->values = $values;
        foreach ($this->fields as $name => $field) {
            if (isset($values[$name])) {
                $field->setValue($values[$name]);
            }
        }
        return $this;
    }

    /**
     * Set validation errors
     */
    public function errors(array $errors): self
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * Get a specific field
     */
    public function getField(string $name): ?Field
    {
        return $this->fields[$name] ?? null;
    }

    /**
     * Get all fields
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Get form name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get form method
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Get form action
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * Get form attributes
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Get theme
     */
    public function getTheme(): string
    {
        return $this->theme;
    }

    /**
     * Get validation errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get error for specific field
     */
    public function getError(string $fieldName): ?string
    {
        return $this->errors[$fieldName] ?? null;
    }

    /**
     * Validate form data
     */
    public function validate(array $data): bool
    {
        $this->errors = $this->validator->validate($data, $this->fields);
        return empty($this->errors);
    }

    /**
     * Enable CSRF token protection
     */
    public function withCsrfToken(string $tokenValue, string $tokenName = '_token'): self
    {
        $this->includeCsrfToken = true;
        $this->csrfTokenValue = $tokenValue;
        $this->csrfTokenName = $tokenName;
        return $this;
    }

    /**
     * Check if CSRF token is enabled
     */
    public function hasCsrfToken(): bool
    {
        return $this->includeCsrfToken;
    }

    /**
     * Get CSRF token name
     */
    public function getCsrfTokenName(): string
    {
        return $this->csrfTokenName;
    }

    /**
     * Get CSRF token value
     */
    public function getCsrfTokenValue(): ?string
    {
        return $this->csrfTokenValue;
    }

    /**
     * Render the complete form
     */
    public function render(): string
    {
        return $this->renderer->renderForm($this);
    }

    /**
     * Render a specific field
     */
    public function renderField(string $fieldName): string
    {
        $field = $this->getField($fieldName);
        if (!$field) {
            return '';
        }
        return $this->renderer->renderField($field, $this->errors[$fieldName] ?? null);
    }

    /**
     * Get form as array (for API responses)
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'method' => $this->method,
            'action' => $this->action,
            'theme' => $this->theme,
            'fields' => array_map(fn($field) => $field->toArray(), $this->fields),
            'errors' => $this->errors,
        ];
    }

    /**
     * Get form as JSON
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}

