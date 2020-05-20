<?php

namespace shop\jobs;

use shop\dispatchers\EventDispatcher;

class AsyncEventJobHandler
{
    private $_dispatcher;

    public function __construct(EventDispatcher $dispatcher)
    {
        $this->_dispatcher = $dispatcher;
    }

    public function handle(AsyncEventJob $job): void
    {
        $this->_dispatcher->dispatch($job->event);
    }
}