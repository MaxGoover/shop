<?php

namespace shop\useCases\manage\Shop;

use shop\entities\Shop\Order\CustomerData;
use shop\entities\Shop\Order\DeliveryData;
use shop\forms\manage\Shop\Order\OrderEditForm;
use shop\repositories\Shop\DeliveryMethodRepository;
use shop\repositories\Shop\OrderRepository;

class OrderManageService
{
    private $_orders;
    private $_deliveryMethods;

    public function __construct(OrderRepository $orders, DeliveryMethodRepository $deliveryMethods)
    {
        $this->_orders = $orders;
        $this->_deliveryMethods = $deliveryMethods;
    }

    public function edit($id, OrderEditForm $form): void
    {
        $order = $this->_orders->get($id);

        $order->edit(
            new CustomerData(
                $form->customer->phone,
                $form->customer->name
            ),
            $form->note
        );

        $order->setDeliveryInfo(
            $this->_deliveryMethods->get($form->delivery->method),
            new DeliveryData(
                $form->delivery->index,
                $form->delivery->address
            )
        );

        $this->_orders->save($order);
    }

    public function remove($id): void
    {
        $order = $this->_orders->get($id);
        $this->_orders->remove($order);
    }
}