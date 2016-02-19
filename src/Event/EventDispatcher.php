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
     * @throws \InvalidArgumentException
     */
    protected function validateEvent($eventName)
    {
        if (!in_array($eventName, self::$events)) {
            throw new \InvalidArgumentException(sprintf(
                'The event "%s" is unknown. Valid events are "%s".',
                $eventName,
                implode('", "', self::$events)
            ));
        }
    }

    /**
     * @param string $eventName
     * @param callable $callback
     */
    public function listen($eventName, callable $callback)
    {
        $this->validateEvent($eventName);

        $this->listeners[$eventName][] = $callback;
    }

    /**
     * @param string $eventName
     * @param EventInterface $event
     */
    public function dispatch($eventName, EventInterface $event)
    {
        $this->validateEvent($eventName);

        array_walk($this->listeners[$eventName], [$this, 'dispatchToListener'], $event);
    }

    /**
     * @param callable $callback
     * @param int $i
     * @param mixed $subject
     */
    protected function dispatchToListener(callable $callback, $i, $subject)
    {
        call_user_func_array($callback, [$subject]);
    }
}
