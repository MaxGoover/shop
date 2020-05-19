<?php

namespace shop\dispatchers;

use yii\di\Container;

class SimpleEventDispatcher implements EventDispatcher
{
    private $_container;
    private $_listeners;

    public function __construct(Container $container, array $listeners)
    {
        $this->_container = $container;
        $this->_listeners = $listeners;
    }

    public function dispatch($event): void
    {
        $eventName = \get_class($event);
        if (\array_key_exists($eventName, $this->_listeners)) {
            foreach ($this->_listeners[$eventName] as $listenerClass) {
                $listener = $this->_resolveListener($listenerClass);
                $listener($event);
            }
        }
    }

    public function dispatchAll(array $events): void
    {
        foreach ($events as $event) {
            $this->dispatch($event);
        }
    }

    private function _resolveListener($listenerClass): callable
    {
        return [$this->_container->get($listenerClass), 'handle'];
    }
}