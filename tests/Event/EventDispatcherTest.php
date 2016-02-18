<?php

namespace ThisData\Api\Event;

class EventDispatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * @var bool
     */
    private $hasBeenCalled = false;

    public function setUp()
    {
        $this->dispatcher = new EventDispatcher();
    }

    public function testInit()
    {
        $listeners = $this->getListeners();

        $this->assertCount(count(EventDispatcher::$events), $listeners);
        $this->assertArrayHasKey(EventDispatcherInterface::EVENT_REQUEST_SUCCESS, $listeners);
        $this->assertArrayHasKey(EventDispatcherInterface::EVENT_REQUEST_ERROR, $listeners);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testListenInvalidEvent()
    {
        $this->dispatcher->listen('foo', [$this, 'handler']);
    }

    public function testListen()
    {
        $this->dispatcher->listen(EventDispatcherInterface::EVENT_REQUEST_SUCCESS, [$this, 'handler']);
        $listeners = $this->getListeners();
        $this->assertCount(1, $listeners[EventDispatcherInterface::EVENT_REQUEST_SUCCESS]);

        $this->dispatcher->listen(EventDispatcherInterface::EVENT_REQUEST_SUCCESS, [$this, 'handler']);
        $listeners = $this->getListeners();
        $this->assertCount(2, $listeners[EventDispatcherInterface::EVENT_REQUEST_SUCCESS]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDispatchInvalidEvent()
    {
        $event = new Event();
        $this->dispatcher->dispatch('foo', $event);
    }

    public function testDispatch()
    {
        $this->dispatcher->listen(EventDispatcherInterface::EVENT_REQUEST_SUCCESS, [$this, 'handler']);

        $this->assertFalse($this->hasBeenCalled);
        $this->dispatcher->dispatch(EventDispatcherInterface::EVENT_REQUEST_SUCCESS, new Event());
        $this->assertTrue($this->hasBeenCalled);
    }

    protected function getListeners()
    {
        $refListeners = new \ReflectionProperty($this->dispatcher, 'listeners');
        $refListeners->setAccessible(true);
        return $refListeners->getValue($this->dispatcher);
    }

    public function handler()
    {
        $this->hasBeenCalled = true;
    }
}
