<?php

namespace shop\useCases\manage\Shop;

use shop\entities\Shop\DeliveryMethod;
use shop\forms\manage\Shop\DeliveryMethodForm;
use shop\repositories\Shop\DeliveryMethodRepository;

class DeliveryMethodManageService
{
    private $_methods;

    public function __construct(DeliveryMethodRepository $methods)
    {
        $this->_methods = $methods;
    }

    public function create(DeliveryMethodForm $form): DeliveryMethod
    {
        $method = DeliveryMethod::create(
            $form->name,
            $form->cost,
            $form->minWeight,
            $form->maxWeight,
            $form->sort
        );
        $this->_methods->save($method);
        return $method;
    }

    public function edit($id, DeliveryMethodForm $form): void
    {
        $method = $this->_methods->get($id);
        $method->edit(
            $form->name,
            $form->cost,
            $form->minWeight,
            $form->maxWeight,
            $form->sort
        );
        $this->_methods->save($method);
    }

    public function remove($id): void
    {
        $method = $this->_methods->get($id);
        $this->_methods->remove($method);
    }
}