<?php

namespace shop\cart\cost;

final class Discount
{
    private $_value;
    private $_name;

    public function __construct(float $value, string $name)
    {
        $this->_value = $value;
        $this->_name = $name;
    }

    public function getName(): string
    {
        return $this->_name;
    }

    public function getValue(): float
    {
        return $this->_value;
    }
}