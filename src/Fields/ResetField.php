<?php

namespace Tweekersnut\FormsLib\Fields;

class ResetField extends Field
{
    protected string $inputType = 'reset';

    public function __construct(string $name = 'reset', string $label = 'Reset')
    {
        parent::__construct($name, 'reset');
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

