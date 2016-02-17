<?php

namespace ThisData\Api\Event;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;

class EventTest extends \PHPUnit_Framework_TestCase
{
    public function testErrorEvent()
    {
        $request = new Request('get', '/');
        $response = new Response(400);

        $ex = new RequestException('Bad request', $request, $response);

        $event = Event::error($ex);
        $this->assertSame($response, $event->getResponse());
    }

    /**
     * @dataProvider getSuccessfulResponses
     */
    public function testIsSuccessful($response)
    {
        $event = Event::success($response);
        $this->assertTrue($event->isSuccessful());
    }

    public function getSuccessfulResponses()
    {
        return [
            [$this->getResponse(200)], // Standard 200 is success
            [$this->getResponse(204)], // Non standard 200 range is success
        ];
    }

    /**
     * @dataProvider getUnsuccessfulExceptions
     */
    public function testIsNotSuccessful(RequestException $exception)
    {
        $event = Event::error($exception);
        $this->assertFalse($event->isSuccessful());
    }

    public function getUnsuccessfulExceptions()
    {
        $request = new Request('get', '/');

        return [
            [new RequestException('Error', $request, $this->getResponse(400))],
            [new RequestException('Error', $request, $this->getResponse(500))],
            [new RequestException('Error', $request)],
        ];
    }

    protected function getResponse($code)
    {
        return new Response($code);
    }

    protected function getException($code, RequestInterface $request = null, $hasResponse = true)
    {
        if (null === $request) {
            $request = new Request('GET', '/');
        }

        if (true === $hasResponse) {
            $response = $this->getResponse($code);
        } else {
            $response = null;
        }

        return new RequestException('Error', $request, $response);
    }
}
