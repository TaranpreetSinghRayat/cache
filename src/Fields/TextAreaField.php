<?php

namespace Tweekersnut\FormsLib\Fields;

class TextAreaField extends Field
{
    protected int $rows = 4;
    protected int $cols = 50;

    public function __construct(string $name)
    {
        parent::__construct($name, 'textarea');
    }

    public function rows(int $rows): self
    {
        $this->rows = $rows;
        $this->customAttributes['rows'] = $rows;
        return $this;
    }

    public function cols(int $cols): self
    {
        $this->cols = $cols;
        $this->customAttributes['cols'] = $cols;
        return $this;
    }

    public function getRows(): int
    {
        return $this->rows;
    }

    public function getCols(): int
    {
        return $this->cols;
    }

    public function toArray(): array
    {
        $data = parent::toArray();
        $data['rows'] = $this->rows;
        $data['cols'] = $this->cols;
        return $data;
    }
}

