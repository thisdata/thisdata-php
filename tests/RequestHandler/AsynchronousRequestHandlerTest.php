<?php

namespace ThisData\Api\RequestHandler;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7\Request;
use ThisData\Api\ResponseManager\AssuredResponseManager;

class AsynchronousRequestHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testHandle()
    {
        $responseManager = $this->getMockBuilder(AssuredResponseManager::class)
            ->getMock();

        $client = $this->getMockBuilder(Client::class)
            ->getMock();

        $request = new Request('get', 'uri');

        $expectedPromise = new Promise();

        $client->expects($this->once())
            ->method('sendAsync')
            ->with($request)
            ->will($this->returnValue($expectedPromise));

        $responseManager->expects($this->once())
            ->method('manageResponse')
            ->with($expectedPromise);

        $handler = new AsynchronousRequestHandler($responseManager);
        $handler->handle($client, $request);
    }
}
