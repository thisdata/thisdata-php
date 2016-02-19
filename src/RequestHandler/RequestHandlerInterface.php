<?php

namespace ThisData\Api\RequestHandler;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

/**
 * Responsible for handling an event to be delivered to ThisData.
 */
interface RequestHandlerInterface
{
    /**
     * @param ResponseInterface $response
     */
    public function handleSuccess(ResponseInterface $response);

    /**
     * @param RequestException $e
     */
    public function handleError(RequestException $e);

    /**
     * @param Client $client
     * @param Request $request
     */
    public function handle(Client $client, Request $request);
}
