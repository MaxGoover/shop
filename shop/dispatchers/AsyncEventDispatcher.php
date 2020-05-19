<?php

namespace shop\dispatchers;

use shop\jobs\AsyncEventJob;
use yii\queue\Queue;

class AsyncEventDispatcher implements EventDispatcher
{
    private $_queue;

    public function __construct(Queue $queue)
    {
        $this->_queue = $queue;
    }

    public function dispatch($event): void
    {
        $this->_queue->push(new AsyncEventJob($event));
    }

    public function dispatchAll(array $events): void
    {
        foreach ($events as $event) {
            $this->dispatch($event);
        }
    }
}