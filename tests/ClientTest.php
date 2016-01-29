<?php

namespace ThisData\Api;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    const DUMMY_API_KEY  = 'dummyapikey';
    const DUMMY_VERSION  = '99';
    const DUMMY_ENDPOINT = 'events';

    public function testBaseUri()
    {
        // The trailing slash is important
        $expectedBaseUri = 'https://api.thisdata.com/v99/';

        $client = new Client(self::DUMMY_API_KEY, self::DUMMY_VERSION);

        $baseUri = $client->getConfig('base_uri');

        $this->assertEquals($expectedBaseUri, $baseUri);
    }

    /**
     * Ensure a request is actually executed
     *
     * @return Request
     */
    public function testRequestHandled()
    {
        // The response doesn't matter, we just don't want to make a real request to the server
        $mockResponses = [
            new Response(200),
        ];

        /** @var Request[] $container */
        $container = [];
        $handler = HandlerStack::create(new MockHandler($mockResponses));
        $handler->push(Middleware::history($container));

        $options = [
            'handler' => $handler
        ];

        $client = new Client(self::DUMMY_API_KEY, self::DUMMY_VERSION, $options);

        $client->request('POST', self::DUMMY_ENDPOINT, ['body' => json_encode(['verb' => 'log-in'])]);

        $this->assertCount(1, $container);

        return $container[0]['request'];
    }

    /**
     * Ensure the API key is appended to a request URL automatically
     * @depends testRequestHandled
     * @param Request $request
     */
    public function testApiKeyQueryParameter(Request $request)
    {
        $expectedUri = sprintf('https://api.thisdata.com/v%s/%s', self::DUMMY_VERSION, self::DUMMY_ENDPOINT);

        $this->assertEquals($expectedUri, (string)$request->getUri());
    }

    /**
     * Ensure the user agent is set to the library name.
     *
     * @param Request $request
     * @depends testRequestHandled
     */
    public function testUserAgentSet(Request $request)
    {
        $this->assertHeaderValue($request, 'User-Agent', Client::HEADER_USER_AGENT);
    }

    /**
     * Ensure the content type is set to application/json.
     *
     * @param Request $request
     * @depends testRequestHandled
     */
    public function testContentType(Request $request)
    {
        $this->assertHeaderValue($request, 'Content-Type', Client::HEADER_CONTENT_TYPE);
    }

    /**
     * Ensure a single header value is set correctly.
     *
     * @param Request $request
     * @param string $header
     * @param string $value
     */
    protected function assertHeaderValue(Request $request, $header, $value)
    {
        $headerValue = $request->getHeader($header);

        $this->assertCount(1, $headerValue);
        $this->assertEquals($value, $headerValue[0]);
    }
}
