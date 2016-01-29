<?php

namespace ThisData\Api;

use ThisData\Api\RequestHandler\AsynchronousRequestHandler;
use ThisData\Api\RequestHandler\RequestHandlerInterface;
use ThisData\Api\RequestHandler\SynchronousRequestHandler;
use ThisData\Api\ResponseManager\AssuredResponseManager;
use ThisData\Api\ResponseManager\ResponseManagerInterface;

/**
 * Builds an instance of ThisData.
 *
 * Given an API key from ThisData, this class provides a fluent programmatic
 * interface for building an abstracted client capable of interacting with
 * the ThisData API.
 */
class Builder
{
    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $version = '1';

    /**
     * @var bool
     */
    private $async = true;

    /**
     * @var array
     */
    private $clientOptions = [];

    /**
     * @var ResponseManagerInterface
     */
    private $responseManager;

    /**
     * @var RequestHandlerInterface
     */
    private $requestHandler;

    /**
     * @param string $apiKey
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Set the ThisData API version to use. Defaults to 1.
     *
     * @param string $version
     * @return Builder
     */
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * Set whether to send requests asynchronously or synchronously.
     *
     * @param boolean $async
     * @return Builder
     */
    public function setAsync($async)
    {
        $this->async = $async;
        return $this;
    }

    /**
     * Set arbitrary options on the Guzzle Client that will interact with the
     * ThisData API.
     *
     * @param string $option
     * @param mixed $value
     * @return $this
     */
    public function setClientOption($option, $value)
    {
        $this->clientOptions[$option] = $value;
        return $this;
    }

    /**
     * Configure the response manager that manages asynchronous responses.
     *
     * @param ResponseManagerInterface $responseManager
     * @return $this
     */
    public function setResponseManager(ResponseManagerInterface $responseManager)
    {
        $this->responseManager = $responseManager;
        return $this;
    }

    /**
     * @return ResponseManagerInterface
     */
    protected function getResponseManager()
    {
        if (null === $this->responseManager) {
            $this->responseManager = $this->buildResponseManager();
        }

        return $this->responseManager;
    }

    /**
     * @return AssuredResponseManager
     */
    protected function buildResponseManager()
    {
        return new AssuredResponseManager();
    }

    /**
     * @return RequestHandlerInterface
     */
    protected function getRequestHandler()
    {
        if (null === $this->requestHandler) {
            $this->requestHandler = $this->buildRequestHandler();
        }

        return $this->requestHandler;
    }

    /**
     * @return RequestHandlerInterface
     */
    protected function buildRequestHandler()
    {
        switch ($this->async) {
            case false:
                return new SynchronousRequestHandler();
            case true:
            default:
                $responseManager = $this->getResponseManager();
                return new AsynchronousRequestHandler($responseManager);
        }
    }

    /**
     * @return Client
     */
    protected function buildClient()
    {
        return new Client($this->apiKey, $this->version, $this->clientOptions);
    }

    /**
     * Create an instance of the ThisData API client abstraction after
     * configuration has been provided.
     *
     * @return ThisData
     */
    public function build()
    {
        $client         = $this->buildClient();
        $requestHandler = $this->getRequestHandler();

        return new ThisData($client, $requestHandler);
    }

    /**
     * Build and return a ThisData instance based on the default configuration.
     *
     * @param string $apiKey
     * @return ThisData
     */
    public static function create($apiKey)
    {
        $class = static::class;

        /** @var Builder $builder */
        $builder = new $class($apiKey);

        return $builder->build();
    }
}
