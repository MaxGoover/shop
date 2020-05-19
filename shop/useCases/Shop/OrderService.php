<?php

namespace shop\useCases\Shop;

use shop\cart\Cart;
use shop\cart\CartItem;
use shop\entities\Shop\Order\CustomerData;
use shop\entities\Shop\Order\DeliveryData;
use shop\entities\Shop\Order\Order;
use shop\entities\Shop\Order\OrderItem;
use shop\forms\Shop\Order\OrderForm;
use shop\repositories\Shop\DeliveryMethodRepository;
use shop\repositories\Shop\OrderRepository;
use shop\repositories\Shop\ProductRepository;
use shop\repositories\UserRepository;
use shop\services\TransactionManager;

class OrderService
{
    private $_cart;
    private $_orders;
    private $_products;
    private $_users;
    private $_deliveryMethods;
    private $_transaction;

    public function __construct(
        Cart $cart,
        OrderRepository $orders,
        ProductRepository $products,
        UserRepository $users,
        DeliveryMethodRepository $deliveryMethods,
        TransactionManager $transaction
    )
    {
        $this->_cart = $cart;
        $this->_orders = $orders;
        $this->_products = $products;
        $this->_users = $users;
        $this->_deliveryMethods = $deliveryMethods;
        $this->_transaction = $transaction;
    }

    public function checkout($userId, OrderForm $form): Order
    {
        $user = $this->_users->get($userId);

        $products = [];

        $items = \array_map(function (CartItem $item) use (&$products) {
            $product = $item->getProduct();
            $product->checkout($item->getModificationId(), $item->getQuantity());
            $products[] = $product;
            return OrderItem::create(
                $product,
                $item->getModificationId(),
                $item->getPrice(),
                $item->getQuantity()
            );
        }, $this->_cart->getItems());

        $order = Order::create(
            $user->id,
            new CustomerData(
                $form->customer->phone,
                $form->customer->name
            ),
            $items,
            $this->_cart->getCost()->getTotal(),
            $form->note
        );

        $order->setDeliveryInfo(
            $this->_deliveryMethods->get($form->delivery->method),
            new DeliveryData(
                $form->delivery->index,
                $form->delivery->address
            )
        );

        $this->_transaction->wrap(function () use ($order, $products) {
            $this->_orders->save($order);
            foreach ($products as $product) {
                $this->_products->save($product);
            }
            $this->_cart->clear();
        });

        return $order;
    }
}