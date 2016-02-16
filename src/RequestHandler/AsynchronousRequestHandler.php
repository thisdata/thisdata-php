<?php

namespace ThisData\Api\RequestHandler;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;

/**
 * Send API calls to the server asynchronously to avoid blocking execution of the service.
 */
class AsynchronousRequestHandler extends AbstractRequestHandler
{
    /**
     * @var array
     */
    private $promises = [];

    /**
     * @param Client $client
     * @param Request $request
     */
    public function handle(Client $client, Request $request)
    {
        $this->promises = $this->send($client, $request);
    }

    /**
     * @param Promise $promise
     */
    protected function wait(Promise $promise)
    {
        if (PromiseInterface::PENDING === $promise->getState()) {
            $promise->wait();
        }
    }

    public function __destruct()
    {
        array_walk($this->promises, [$this, 'wait']);
    }
}
