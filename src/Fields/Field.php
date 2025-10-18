<?php

namespace Tweekersnut\FormsLib\Fields;

abstract class Field
{
    protected string $name;
    protected string $type;
    protected string $label = '';
    protected mixed $value = null;
    protected array $attributes = [];
    protected array $rules = [];
    protected bool $required = false;
    protected string $placeholder = '';
    protected string $helpText = '';
    protected array $customAttributes = [];

    public function __construct(string $name, string $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * Set field label
     */
    public function label(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Set field value
     */
    public function value(mixed $value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Set field placeholder
     */
    public function placeholder(string $placeholder): self
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    /**
     * Set help text
     */
    public function help(string $helpText): self
    {
        $this->helpText = $helpText;
        return $this;
    }

    /**
     * Mark field as required
     */
    public function required(bool $required = true): self
    {
        $this->required = $required;
        if ($required && !in_array('required', $this->rules)) {
            $this->rules[] = 'required';
        }
        return $this;
    }

    /**
     * Add validation rule
     */
    public function rule(string $rule): self
    {
        if (!in_array($rule, $this->rules)) {
            $this->rules[] = $rule;
        }
        return $this;
    }

    /**
     * Add multiple validation rules
     */
    public function rules(array $rules): self
    {
        foreach ($rules as $rule) {
            $this->rule($rule);
        }
        return $this;
    }

    /**
     * Add custom HTML attributes
     */
    public function attributes(array $attributes): self
    {
        $this->customAttributes = array_merge($this->customAttributes, $attributes);
        return $this;
    }

    /**
     * Add custom class
     */
    public function addClass(string $class): self
    {
        if (!isset($this->customAttributes['class'])) {
            $this->customAttributes['class'] = '';
        }
        $this->customAttributes['class'] .= ' ' . $class;
        return $this;
    }

    /**
     * Add custom ID
     */
    public function id(string $id): self
    {
        $this->customAttributes['id'] = $id;
        return $this;
    }

    /**
     * Get field name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get field type
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get input type (for rendering)
     */
    public function getInputType(): string
    {
        return $this->type;
    }

    /**
     * Get field label
     */
    public function getLabel(): string
    {
        return $this->label ?: ucfirst(str_replace('_', ' ', $this->name));
    }

    /**
     * Get field value
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Get field placeholder
     */
    public function getPlaceholder(): string
    {
        return $this->placeholder;
    }

    /**
     * Get help text
     */
    public function getHelpText(): string
    {
        return $this->helpText;
    }

    /**
     * Check if field is required
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * Get validation rules
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * Get custom attributes
     */
    public function getAttributes(): array
    {
        return $this->customAttributes;
    }

    /**
     * Get attribute value
     */
    public function getAttribute(string $key): ?string
    {
        return $this->customAttributes[$key] ?? null;
    }

    /**
     * Convert field to array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'label' => $this->getLabel(),
            'value' => $this->value,
            'placeholder' => $this->placeholder,
            'helpText' => $this->helpText,
            'required' => $this->required,
            'rules' => $this->rules,
            'attributes' => $this->customAttributes,
        ];
    }
}

