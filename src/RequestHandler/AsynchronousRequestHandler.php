<?php

namespace ThisData\Api\RequestHandler;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7\Request;
use ThisData\Api\Event\EventDispatcherInterface;

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
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        parent::__construct($dispatcher);

        register_shutdown_function([$this, 'waitForResponses']);
    }

    /**
     * @param Client $client
     * @param Request $request
     */
    public function handle(Client $client, Request $request)
    {
        // TODO memory leak. Promises are never removed/GC'd.
        $this->promises[] = $this->send($client, $request);
    }

    /**
     * @param Promise $promise
     */
    protected function wait(Promise $promise)
    {
        $promise->wait();
    }

    public function waitForResponses()
    {
        array_walk($this->promises, [$this, 'wait']);
    }
}
