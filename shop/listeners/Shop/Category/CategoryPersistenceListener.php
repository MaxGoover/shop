<?php

namespace shop\listeners\Shop\Category;

use shop\entities\Blog\Category;
use shop\repositories\events\EntityPersisted;
use yii\caching\Cache;
use yii\caching\TagDependency;

class CategoryPersistenceListener
{
    private $_cache;

    public function __construct(Cache $cache)
    {
        $this->_cache = $cache;
    }

    public function handle(EntityPersisted $event): void
    {
        if ($event->entity instanceof Category) {
            TagDependency::invalidate($this->_cache, ['categories']);
        }
    }
}