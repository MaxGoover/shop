<?php

namespace shop\entities;

trait EventTrait
{
    private $_events = [];

    public function releaseEvents(): array
    {
        $events = $this->_events;
        $this->_events = [];
        return $events;
    }

    protected function recordEvent($event): void
    {
        $this->_events[] = $event;
    }
}