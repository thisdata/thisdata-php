<?php

namespace ThisData\Api\RequestHandler;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use ThisData\Api\ResponseManager\ResponseManagerInterface;

/**
 * Send API calls to the server asynchronously to avoid blocking execution of the service.
 */
class AsynchronousRequestHandler implements RequestHandlerInterface
{
    /**
     * @var ResponseManagerInterface
     */
    private $responseManager;

    /**
     * @param ResponseManagerInterface $responseManager
     */
    public function __construct(ResponseManagerInterface $responseManager)
    {
        $this->responseManager = $responseManager;
    }

    /**
     * @param Client $client
     * @param Request $request
     */
    public function handle(Client $client, Request $request)
    {
        $this->responseManager->manageResponse($client->sendAsync($request));
    }
}
