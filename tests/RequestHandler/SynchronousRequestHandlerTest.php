<?php

namespace ThisData\Api\RequestHandler;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7\Request;
use ThisData\Api\Event\EventDispatcher;
use ThisData\Api\Event\EventDispatcherInterface;

class SynchronousRequestHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $dispatcher;

    /**
     * @var SynchronousRequestHandler
     */
    private $handler;

    public function setUp()
    {
        $this->dispatcher = $this->getMock(EventDispatcher::class);

        $this->handler = new SynchronousRequestHandler($this->dispatcher);
    }

    public function testHandle()
    {
        $request = new Request('GET', '/');
        $promise = $this->getMockBuilder(Promise::class)
            ->setMethods(['then', 'wait'])
            ->getMock();

        $promise->expects($this->once())
            ->method('then')
            ->willReturn($promise);

        $promise->expects($this->once())
            ->method('wait');

        $client = $this->getMockBuilder(Client::class)
            ->setMethods(['sendAsync'])
            ->getMock();

        $client->expects($this->once())
            ->method('sendAsync')
            ->with($request)
            ->willReturn($promise);

        $this->handler->handle($client, $request);
    }
}
