<?php

namespace ThisData\Api\RequestHandler;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7\Request;

class AsynchronousRequestHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testHandle()
    {
        $client = $this->getMockBuilder(Client::class)
            ->getMock();

        $request = new Request('get', 'uri');

        $expectedPromise = new Promise();

        $client->expects($this->once())
            ->method('sendAsync')
            ->with($request)
            ->will($this->returnValue($expectedPromise));

        $handler = new AsynchronousRequestHandler();
        $handler->handle($client, $request);
    }
}
