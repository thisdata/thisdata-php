<?php

namespace ThisData\Api\ResponseManager;

use GuzzleHttp\Promise\PromiseInterface;

/**
 * Guarantees asynchronous requests have completed before system shutdown at
 * the end of a request.
 */
class AssuredResponseManager implements ResponseManagerInterface
{
    /**
     * @var array
     */
    private $promises = [];

    public function __construct()
    {
        register_shutdown_function([$this, 'onShutdown']);
    }

    /**
     * Track a response to ensure its completion; successful or otherwise.
     *
     * @param PromiseInterface $promise
     */
    public function manageResponse(PromiseInterface $promise)
    {
        $this->promises[] = $promise;
    }

    /**
     * Ensure an asynchronous request is completed before terminating.
     *
     * @param PromiseInterface $promise
     */
    protected function handlePromise(PromiseInterface $promise)
    {
        if (PromiseInterface::PENDING === $promise->getState()) {
            $promise->wait();
        }
    }

    /**
     * When PHP shuts down at the end of the request, ensure all promises have
     * been fulfilled.
     */
    public function onShutdown()
    {
        array_walk($this->promises, [$this, 'handlePromise']);
    }
}
