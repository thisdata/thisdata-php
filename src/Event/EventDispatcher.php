<?php

namespace ThisData\Api\Event;

use GuzzleHttp\Psr7\Response;

class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var array
     */
    public static $events = [
        self::EVENT_REQUEST_SUCCESS,
        self::EVENT_REQUEST_ERROR
    ];

    /**
     * @var array
     */
    private $listeners = [];

    public function __construct()
    {
        $this->init();
    }

    protected function init()
    {
        foreach (self::$events as $eventName) {
            $this->listeners[$eventName] = [];
        }
    }

    /**
     * @param string $eventName
     * @param callable $callback
     */
    public function listen($eventName, callable $callback)
    {
        if (!in_array($eventName, self::$events)) {
            throw new \InvalidArgumentException(sprintf(
                'The event "%s" is unknown. Valid events are "%s".',
                $eventName,
                implode('", "', self::$events)
            ));
        }

        $this->listeners[$eventName] = $callback;
    }

    /**
     * @param string $eventName
     * @param EventInterface $event
     */
    public function dispatch($eventName, EventInterface $event)
    {
        array_walk($this->listeners[$eventName], [$this, 'dispatchToListener'], $event->getResponse());
    }

    /**
     * @param callable $callback
     * @param int $i
     * @param Response $response
     */
    protected function dispatchToListener(callable $callback, $i, Response $response)
    {
        call_user_func_array($callback, [$response]);
    }
}
