<?php

namespace Tweekersnut\FormsLib\Fields;

class TextField extends Field
{
    protected string $inputType = 'text';

    public function __construct(string $name, string $inputType = 'text')
    {
        parent::__construct($name, 'text');
        $this->inputType = $inputType;
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

class EmailField extends TextField
{
    public function __construct(string $name)
    {
        parent::__construct($name, 'email');
        $this->rule('email');
    }
}

class PasswordField extends TextField
{
    public function __construct(string $name)
    {
        parent::__construct($name, 'password');
    }
}

class NumberField extends TextField
{
    public function __construct(string $name)
    {
        parent::__construct($name, 'number');
        $this->rule('numeric');
    }
}

class PhoneField extends TextField
{
    public function __construct(string $name)
    {
        parent::__construct($name, 'tel');
        $this->rule('phone');
    }
}

class URLField extends TextField
{
    public function __construct(string $name)
    {
        parent::__construct($name, 'url');
        $this->rule('url');
    }
}

class DateField extends TextField
{
    public function __construct(string $name)
    {
        parent::__construct($name, 'date');
        $this->rule('date');
    }
}

class TimeField extends TextField
{
    public function __construct(string $name)
    {
        parent::__construct($name, 'time');
    }
}

class DateTimeField extends TextField
{
    public function __construct(string $name)
    {
        parent::__construct($name, 'datetime-local');
        $this->rule('datetime');
    }
}

class ColorField extends TextField
{
    public function __construct(string $name)
    {
        parent::__construct($name, 'color');
    }
}

class RangeField extends TextField
{
    protected int $min = 0;
    protected int $max = 100;

    public function __construct(string $name)
    {
        parent::__construct($name, 'range');
    }

    public function min(int $min): self
    {
        $this->min = $min;
        $this->customAttributes['min'] = $min;
        return $this;
    }

    public function max(int $max): self
    {
        $this->max = $max;
        $this->customAttributes['max'] = $max;
        return $this;
    }

    public function step(int $step): self
    {
        $this->customAttributes['step'] = $step;
        return $this;
    }

    public function toArray(): array
    {
        $data = parent::toArray();
        $data['min'] = $this->min;
        $data['max'] = $this->max;
        return $data;
    }
}

