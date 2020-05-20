<?php

namespace shop\cart\storage;

use yii\web\Session;

class SessionStorage implements StorageInterface
{
    private $_key;
    private $_session;

    public function __construct($key, Session $session)
    {
        $this->_key = $key;
        $this->_ = $session;
    }

    public function load(): array
    {
        return $this->_session->get($this->_key, []);
    }

    public function save(array $items): void
    {
        $this->_session->set($this->_key, $items);
    }
} 