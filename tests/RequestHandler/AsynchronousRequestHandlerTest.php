<?php

namespace ThisData\Api\RequestHandler;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use ThisData\Api\Event\EventDispatcher;

class AsynchronousRequestHandlerTest extends \PHPUnit_Framework_TestCase
{
    const API_KEY = 'apikey';

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $dispatcher;

    /**
     * @var AsynchronousRequestHandler
     */
    private $handler;

    public function setUp()
    {
        $this->dispatcher = $this->getMockBuilder(EventDispatcher::class)
            ->getMock();

        $this->handler = new AsynchronousRequestHandler($this->dispatcher);
    }

    public function testHandle()
    {
        $client = $this->getMockClient();
        $request = new Request('GET', '/');

        $this->handler->handle($client, $request);
        $this->assertAttributeCount(1, 'promises', $this->handler);
    }

    public function testWaitForResponses()
    {
        $promise = $this->getMockBuilder(Promise::class)
            ->setMethods(['wait'])
            ->getMock();

        $promise->expects($this->once())
            ->method('wait');

        $property = new \ReflectionProperty($this->handler, 'promises');
        $property->setAccessible(true);
        $property->setValue($this->handler, [$promise]);

        $this->handler->waitForResponses();
    }

    protected function getMockClient()
    {
        $mock = new MockHandler([
            new Response(200),
        ]);

        $handler = HandlerStack::create($mock);

        return new \ThisData\Api\Client(self::API_KEY, 1, [
            'handler' => $handler,
        ]);
    }
}
