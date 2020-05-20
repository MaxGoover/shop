<?php

namespace shop\repositories\Shop;

use shop\dispatchers\EventDispatcher;
use shop\entities\Shop\Product\Product;
use shop\repositories\events\EntityPersisted;
use shop\repositories\events\EntityRemoved;
use shop\repositories\NotFoundException;

class ProductRepository
{
    private $_eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->_eventDispatcher = $eventDispatcher;
    }

    public function existsByBrand($id): bool
    {
        return Product::find()->andWhere(['brand_id' => $id])->exists();
    }

    public function existsByMainCategory($id): bool
    {
        return Product::find()->andWhere(['category_id' => $id])->exists();
    }

    public function get($id): Product
    {
        if (!$product = Product::findOne($id)) {
            throw new NotFoundException('Product is not found.');
        }
        return $product;
    }

    public function remove(Product $product): void
    {
        if (!$product->delete()) {
            throw new \RuntimeException('Removing error.');
        }
        $this->_eventDispatcher->dispatchAll($product->releaseEvents());
        $this->_eventDispatcher->dispatch(new EntityRemoved($product));
    }

    public function save(Product $product): void
    {
        if (!$product->save()) {
            throw new \RuntimeException('Saving error.');
        }
        $this->_eventDispatcher->dispatchAll($product->releaseEvents());
        $this->_eventDispatcher->dispatch(new EntityPersisted($product));
    }
}