<?php

namespace api\providers;

use yii\base\BaseObject;
use yii\data\DataProviderInterface;
use yii\data\Pagination;
use yii\data\Sort;

/**
 * @property int $count
 * @property array $keys
 * @property array $models
 * @property Pagination|false $pagination
 * @property Sort|bool $sort
 * @property int $totalCount
 */
class MapDataProvider extends BaseObject implements DataProviderInterface
{
    private $_callback;
    private $_next;

    public function __construct(DataProviderInterface $next, callable $callback)
    {
        $this->_callback = $callback;
        $this->_next = $next;
        parent::__construct();
    }

    public function getCount(): int
    {
        return $this->_next->getCount();
    }

    public function getKeys(): array
    {
        return $this->_next->getKeys();
    }

    public function getModels(): array
    {
        return \array_map($this->_callback, $this->_next->getModels());
    }

    public function getSort()
    {
        return $this->_next->getSort();
    }

    public function getPagination()
    {
        return $this->_next->getPagination();
    }

    public function getTotalCount(): int
    {
        return $this->_next->getTotalCount();
    }

    public function prepare($forcePrepare = false): void
    {
        $this->_next->prepare($forcePrepare);
    }
}