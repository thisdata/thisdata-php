<?php

namespace ThisData\Api\RequestHandler;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

/**
 * Send events to ThisData in real time, blocking execution of the service
 * until the request/response cycle is complete.
 */
class SynchronousRequestHandler implements RequestHandlerInterface
{
    /**
     * @param Client $client
     * @param Request $request
     */
    public function handle(Client $client, Request $request)
    {
        $client->send($request);
    }
}
