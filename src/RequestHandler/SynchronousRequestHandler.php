<?php

namespace ThisData\Api\RequestHandler;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class SynchronousRequestHandler implements RequestHandlerInterface
{
    /**
     * Send the http request synchronously
     *
     * @param Client $client
     * @param Request $request
     */
    public function handle(Client $client, Request $request)
    {
        $client->send($request);
    }
}
