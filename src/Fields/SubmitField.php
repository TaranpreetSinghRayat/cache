<?php

namespace Tweekersnut\FormsLib\Fields;

class SubmitField extends Field
{
    protected string $inputType = 'submit';

    public function __construct(string $name = 'submit', string $label = 'Submit')
    {
        parent::__construct($name, 'submit');
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

