<?php

namespace shop\cart\cost\calculator;

use shop\cart\cost\Cost;
use shop\cart\cost\Discount as CartDiscount;
use shop\entities\Shop\Discount as EntityDiscount;

class DynamicCost implements CalculatorInterface
{
    private $_calculator;

    public function __construct(CalculatorInterface $calculator)
    {
        $this->_calculator = $calculator;
    }

    public function getCost(array $items): Cost
    {
        /** @var EntityDiscount[] $discounts */
        $discounts = EntityDiscount::find()->active()->orderBy('sort')->all();

        $cost = $this->_calculator->getCost($items);

        foreach ($discounts as $discount) {
            if ($discount->isEnabled()) {
                $new = new CartDiscount($cost->getOrigin() * $discount->percent / 100, $discount->name);
                $cost = $cost->withDiscount($new);
            }
        }

        return $cost;
    }
}