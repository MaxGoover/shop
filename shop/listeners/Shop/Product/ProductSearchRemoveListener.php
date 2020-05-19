<?php

namespace shop\listeners\Shop\Product;

use shop\entities\Shop\Product\Product;
use shop\repositories\events\EntityRemoved;
use shop\services\search\ProductIndexer;
use yii\caching\Cache;
use yii\caching\TagDependency;

class ProductSearchRemoveListener
{
    private $_productIndexer;
    private $_cache;

    public function __construct(ProductIndexer $productIndexer, Cache $cache)
    {
        $this->_productIndexer = $productIndexer;
        $this->_cache = $cache;
    }

    public function handle(EntityRemoved $event): void
    {
        if ($event->entity instanceof Product) {
            $this->_productIndexer->remove($event->entity);
            TagDependency::invalidate($this->_cache, ['products']);
        }
    }
}