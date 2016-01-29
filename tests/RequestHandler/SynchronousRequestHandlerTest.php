<?php

namespace ThisData\Api\RequestHandler;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class SynchronousRequestHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testHandle()
    {
        $client = $this->getMockBuilder(Client::class)
            ->getMock();

        $request = new Request('get', 'uri');

        $client->expects($this->once())
            ->method('send')
            ->with($request);

        $handler = new SynchronousRequestHandler();
        $handler->handle($client, $request);
    }
}
