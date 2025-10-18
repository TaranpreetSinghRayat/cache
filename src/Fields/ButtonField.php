<?php

namespace Tweekersnut\FormsLib\Fields;

class ButtonField extends Field
{
    protected string $inputType = 'button';

    public function __construct(string $name, string $label = 'Button')
    {
        parent::__construct($name, 'button');
        $this->label = $label;
        $this->value = $label;
    }

    public function getInputType(): string
    {
        return $this->inputType;
    }

    public function toArray(): array
    {
        $data = parent::toArray();
        $data['inputType'] = $this->inputType;
        return $data;
    }
}

