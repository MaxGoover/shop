<?php

namespace shop\useCases\cabinet;

use shop\repositories\Shop\ProductRepository;
use shop\repositories\UserRepository;

class WishlistService
{
    private $_users;
    private $_products;

    public function __construct(UserRepository $users, ProductRepository $products)
    {
        $this->_users = $users;
        $this->_products = $products;
    }

    public function add($userId, $productId): void
    {
        $user = $this->_users->get($userId);
        $product = $this->_products->get($productId);
        $user->addToWishList($product->id);
        $this->_users->save($user);
    }

    public function remove($userId, $productId): void
    {
        $user = $this->_users->get($userId);
        $product = $this->_products->get($productId);
        $user->removeFromWishList($product->id);
        $this->_users->save($user);
    }
}