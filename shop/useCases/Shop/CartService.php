<?php

namespace shop\useCases\Shop;

use shop\cart\Cart;
use shop\cart\CartItem;
use shop\repositories\Shop\ProductRepository;

class CartService
{
    private $_cart;
    private $_products;

    public function __construct(Cart $cart, ProductRepository $products)
    {
        $this->_cart = $cart;
        $this->_products = $products;
    }

    public function add($productId, $modificationId, $quantity): void
    {
        $product = $this->_products->get($productId);
        $modId = $modificationId ? $product->getModification($modificationId)->id : null;
        $this->_cart->add(new CartItem($product, $modId, $quantity));
    }

    public function clear(): void
    {
        $this->_cart->clear();
    }

    public function getCart(): Cart
    {
        return $this->_cart;
    }

    public function remove($id): void
    {
        $this->_cart->remove($id);
    }

    public function set($id, $quantity): void
    {
        $this->_cart->set($id, $quantity);
    }
}