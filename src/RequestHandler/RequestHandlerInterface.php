<?php

namespace ThisData\Api\RequestHandler;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

interface RequestHandlerInterface
{
    public function handle(Client $client, Request $request);
}
