<?php

namespace Tweekersnut\FormsLib\Fields;

class CheckboxField extends Field
{
    protected array $options = [];
    protected bool $inline = false;

    public function __construct(string $name)
    {
        parent::__construct($name, 'checkbox');
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

    public function inline(bool $inline = true): self
    {
        $this->inline = $inline;
        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function isInline(): bool
    {
        return $this->inline;
    }

    public function toArray(): array
    {
        $data = parent::toArray();
        $data['options'] = $this->options;
        $data['inline'] = $this->inline;
        return $data;
    }
}

class RadioField extends Field
{
    protected array $options = [];
    protected bool $inline = false;

    public function __construct(string $name)
    {
        parent::__construct($name, 'radio');
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

    public function inline(bool $inline = true): self
    {
        $this->inline = $inline;
        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function isInline(): bool
    {
        return $this->inline;
    }

    public function toArray(): array
    {
        $data = parent::toArray();
        $data['options'] = $this->options;
        $data['inline'] = $this->inline;
        return $data;
    }
}

