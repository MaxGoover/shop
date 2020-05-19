<?php

namespace frontend\widgets\Shop;

use shop\cart\Cart;
use yii\base\Widget;

class CartWidget extends Widget
{
    private $_cart;

    public function __construct(Cart $cart, $config = [])
    {
        parent::__construct($config);
        $this->_cart = $cart;
    }

    public function run(): string
    {
        return $this->render('cart', [
            'cart' => $this->_cart,
        ]);
    }
}