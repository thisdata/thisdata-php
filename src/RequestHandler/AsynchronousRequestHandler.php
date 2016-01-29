<?php

namespace ThisData\Api\RequestHandler;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use ThisData\Api\ResponseManager\ResponseManagerInterface;

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
     * Send the response asynchronously
     *
     * @param Client $client
     * @param Request $request
     */
    public function handle(Client $client, Request $request)
    {
        $this->responseManager->manageResponse($client->sendAsync($request));
    }
}
