<?php

namespace Tweekersnut\FormsLib\Fields;

class SelectField extends Field
{
    protected array $options = [];
    protected bool $multiple = false;

    public function __construct(string $name)
    {
        parent::__construct($name, 'select');
    }

    public function options(array $options): self
    {
        $this->options = $options;
        return $this;
    }

    public function addOption(string $value, string $label): self
    {
        $this->options[$value] = $label;
        return $this;
    }

    public function multiple(bool $multiple = true): self
    {
        $this->multiple = $multiple;
        if ($multiple) {
            $this->customAttributes['multiple'] = 'multiple';
        } else {
            unset($this->customAttributes['multiple']);
        }
        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    public function toArray(): array
    {
        $data = parent::toArray();
        $data['options'] = $this->options;
        $data['multiple'] = $this->multiple;
        return $data;
    }
}

