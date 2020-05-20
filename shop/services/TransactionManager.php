<?php

namespace shop\services;

use shop\dispatchers\DeferredEventDispatcher;

class TransactionManager
{
    private $_deferredEventDispatcher;

    public function __construct(DeferredEventDispatcher $deferredEventDispatcher)
    {
        $this->_deferredEventDispatcher = $deferredEventDispatcher;
    }

    public function wrap(callable $function): void
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $this->_deferredEventDispatcher->defer();
            $function();
            $transaction->commit();
            $this->_deferredEventDispatcher->release();
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->_deferredEventDispatcher->clean();
            throw $e;
        }
    }
}