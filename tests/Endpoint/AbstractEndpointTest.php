<?php

namespace ThisData\Api\Endpoint;

use GuzzleHttp\Client;
use Psr\Http\Message\RequestInterface;
use ThisData\Api\RequestHandler\SynchronousRequestHandler;

class AbstractEndpointTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Ensure values are found, or defaults are returned.
     */
    public function testFindValue()
    {
        $endpoint = new EndpointStub();

        $pool = [
            'foo' => 'bar'
        ];

        $newDefault = 'test';

        $this->assertSame('bar', $endpoint->findValue('foo', $pool), 'Present value returns expected');
        $this->assertSame(null, $endpoint->findValue('bar', $pool), 'Missing value returns default');
        $this->assertSame($newDefault, $endpoint->findValue('bar', $pool, $newDefault), 'Missing value returns specified default');
    }

    /**
     * Ensure serialization functions
     */
    public function testSerialize()
    {
        $endpoint = new EndpointStub();

        $data = [
            'foo' => 'bar'
        ];

        $expectedJson = '{"foo":"bar"}';

        $this->assertSame($expectedJson, $endpoint->serialize($data));
    }

    /**
     * Ensure the handler is called with the expected request.
     */
    public function testExecute()
    {
        $mockClient = new Client();
        $mockHandler = $this->getMockBuilder(SynchronousRequestHandler::class)->getMock();

        $mockHandler
            ->expects($this->once())
            ->method('handle')
            ->with($mockClient, $this->callback(function ($request) {
                return $request instanceof RequestInterface
                    && $request->getMethod()       === 'METHOD'
                    && (string)$request->getUri()  === 'verb'
                    && (string)$request->getBody() === '["data"]';
            }));

        $endpoint = new EndpointStub($mockClient, $mockHandler);
        $endpoint->execute('method', 'verb', ['data']);
    }
}
