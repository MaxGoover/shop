<?php

namespace shop\dispatchers;

class DeferredEventDispatcher implements EventDispatcher
{
    private $_eventDispatcher;
    private $_defer = false;
    private $_queue = [];

    public function __construct(
        EventDispatcher $eventDispatcher,
        $defer = false,
        $queue = [])
    {
        $this->_eventDispatcher = $eventDispatcher;
        $this->_defer = $defer;
        $this->_queue = $queue;
    }

    public function clean(): void
    {
        $this->_queue = [];
        $this->_defer = false;
    }

    public function defer(): void
    {
        $this->_defer = true;
    }

    public function dispatch($event): void
    {
        if ($this->_defer) {
            $this->_queue[] = $event;
        } else {
            $this->_eventDispatcher->dispatch($event);
        }
    }

    public function dispatchAll(array $events): void
    {
        foreach ($events as $event) {
            $this->dispatch($event);
        }
    }

    public function release(): void
    {
        foreach ($this->_queue as $i => $event) {
            $this->_eventDispatcher->dispatch($event);
            unset($this->_queue[$i]);
        }
        $this->_defer = false;
    }
}