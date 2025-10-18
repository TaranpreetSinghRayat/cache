<?php

namespace Tweekersnut\FormsLib\Fields;

class FileField extends Field
{
    protected array $acceptedTypes = [];
    protected bool $multiple = false;

    public function __construct(string $name)
    {
        parent::__construct($name, 'file');
    }

    public function accept(array $types): self
    {
        $this->acceptedTypes = $types;
        $this->customAttributes['accept'] = implode(',', $types);
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

    public function getAcceptedTypes(): array
    {
        return $this->acceptedTypes;
    }

    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    public function toArray(): array
    {
        $data = parent::toArray();
        $data['acceptedTypes'] = $this->acceptedTypes;
        $data['multiple'] = $this->multiple;
        return $data;
    }
}

class HiddenField extends Field
{
    public function __construct(string $name)
    {
        parent::__construct($name, 'hidden');
    }
}

class SubmitField extends Field
{
    public function __construct(string $name = 'submit', string $label = 'Submit')
    {
        parent::__construct($name, 'submit');
        $this->label = $label;
        $this->value = $label;
    }
}

class ResetField extends Field
{
    public function __construct(string $name = 'reset', string $label = 'Reset')
    {
        parent::__construct($name, 'reset');
        $this->label = $label;
        $this->value = $label;
    }
}

class ButtonField extends Field
{
    public function __construct(string $name, string $label = 'Button')
    {
        parent::__construct($name, 'button');
        $this->label = $label;
        $this->value = $label;
    }
}

