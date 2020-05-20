<?php

namespace shop\cart\storage;

use shop\cart\CartItem;
use yii\db\Connection;
use yii\web\User;

class HybridStorage implements StorageInterface
{
    private $_cookieKey;
    private $_cookieTimeout;
    private $_db;
    private $_storage;
    private $_user;

    public function __construct(
        $cookieKey,
        $cookieTimeout,
        Connection $db,
        User $user)
    {
        $this->_user = $user;
        $this->_cookieKey = $cookieKey;
        $this->_cookieTimeout = $cookieTimeout;
        $this->_db = $db;
    }

    public function load(): array
    {
        return $this->_getStorage()->load();
    }

    public function save(array $items): void
    {
        $this->_getStorage()->save($items);
    }

    private function _getStorage()
    {
        if ($this->_storage === null) {
            $cookieStorage = new CookieStorage($this->_cookieKey, $this->_cookieTimeout);
            if ($this->_user->isGuest) {
                $this->_storage = $cookieStorage;
            } else {
                $dbStorage = new DbStorage($this->_user->id, $this->_db);
                if ($cookieItems = $cookieStorage->load()) {
                    $dbItems = $dbStorage->load();
                    $items = \array_merge($dbItems, \array_udiff($cookieItems, $dbItems, function (CartItem $first, CartItem $second) {
                        return $first->getId() === $second->getId();
                    }));
                    $dbStorage->save($items);
                    $cookieStorage->save([]);
                }
                $this->_storage = $dbStorage;
            }
        }
        return $this->_storage;
    }
}