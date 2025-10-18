<?php

namespace Tweekersnut\FormsLib\Fields;

class HiddenField extends Field
{
    protected string $inputType = 'hidden';

    public function __construct(string $name)
    {
        parent::__construct($name, 'hidden');
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

