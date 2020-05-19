<?php

namespace shop\cart;

use shop\cart\cost\calculator\CalculatorInterface;
use shop\cart\cost\Cost;
use shop\cart\storage\StorageInterface;

class Cart
{
    private $_calculator;
    private $_storage;

    /**
     * @var CartItem[]
     * */
    private $_items;

    public function __construct(CalculatorInterface $calculator, StorageInterface $storage)
    {
        $this->_calculator = $calculator;
        $this->_storage = $storage;
    }

    public function add(CartItem $item): void
    {
        $this->_loadItems();
        foreach ($this->_items as $i => $current) {
            if ($current->getId() == $item->getId()) {
                $this->_items[$i] = $current->plus($item->getQuantity());
                $this->_saveItems();
                return;
            }
        }
        $this->_items[] = $item;
        $this->saveItems();
    }

    public function clear(): void
    {
        $this->_items = [];
        $this->_saveItems();
    }

    public function getAmount(): int
    {
        $this->_loadItems();
        return count($this->_items);
    }

    public function getCost(): Cost
    {
        $this->_loadItems();
        return $this->_calculator->getCost($this->_items);
    }

    /**
     * @return CartItem[]
     */
    public function getItems(): array
    {
        $this->_loadItems();
        return $this->_items;
    }

    public function getWeight(): int
    {
        $this->_loadItems();
        return array_sum(array_map(function (CartItem $item) {
            return $item->getWeight();
        }, $this->_items));
    }

    public function remove($id): void
    {
        $this->_loadItems();
        foreach ($this->_items as $i => $current) {
            if ($current->getId() == $id) {
                unset($this->_items[$i]);
                $this->_saveItems();
                return;
            }
        }
        throw new \DomainException('Item is not found.');
    }

    public function set($id, $quantity): void
    {
        $this->_loadItems();
        foreach ($this->_items as $i => $current) {
            if ($current->getId() == $id) {
                $this->_items[$i] = $current->changeQuantity($quantity);
                $this->_saveItems();
                return;
            }
        }
        throw new \DomainException('Item is not found.');
    }

    private function _loadItems(): void
    {
        if ($this->_items === null) {
            $this->_items = $this->_storage->load();
        }
    }

    private function _saveItems(): void
    {
        $this->_storage->save($this->_items);
    }
} 