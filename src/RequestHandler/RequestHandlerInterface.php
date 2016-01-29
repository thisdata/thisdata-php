<?php

namespace ThisData\Api\RequestHandler;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

/**
 * Responsible for handling an event to be delivered to ThisData.
 */
interface RequestHandlerInterface
{
    /**
     * @param Client $client
     * @param Request $request
     */
    public function handle(Client $client, Request $request);
}
