<?php

namespace ThisData\Api\Event;

interface EventDispatcherInterface
{
    const EVENT_REQUEST_SUCCESS = 'thisdata.request.success';
    const EVENT_REQUEST_ERROR   = 'thisdata.request.error';

    /**
     * @param string $eventName
     * @param callable $callback
     */
    public function listen($eventName, callable $callback);

    /**
     * @param string $eventName
     * @param EventInterface $event
     */
    public function dispatch($eventName, EventInterface $event);
}
