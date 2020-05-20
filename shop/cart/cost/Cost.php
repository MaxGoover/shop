<?php

namespace shop\cart\cost;

final class Cost
{
    private $_value;
    private $_discounts = [];

    public function __construct(float $value, array $discounts = [])
    {
        $this->_value = $value;
        $this->_discounts = $discounts;
    }

    /**
     * @return Discount[]
     */
    public function getDiscounts(): array
    {
        return $this->_discounts;
    }

    public function getOrigin(): float
    {
        return $this->_value;
    }

    public function getTotal(): float
    {
        return $this->_value - \array_sum(\array_map(function (Discount $discount) {
            return $discount->getValue();
        }, $this->_discounts));
    }

    public function withDiscount(Discount $discount): self
    {
        return new static($this->_value, \array_merge($this->_discounts, [$discount]));
    }
}