<?php

namespace ThisData\Api\ResponseManager;

use GuzzleHttp\Promise\PromiseInterface;

/**
 * Track asynchronous requests and responses.
 */
interface ResponseManagerInterface
{
    /**
     * @param PromiseInterface $promise
     */
    public function manageResponse(PromiseInterface $promise);
}
