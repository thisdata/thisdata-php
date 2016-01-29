<?php

namespace ThisData\Api;

use ThisData\Api\RequestHandler\AsynchronousRequestHandler;
use ThisData\Api\RequestHandler\RequestHandlerInterface;
use ThisData\Api\RequestHandler\SynchronousRequestHandler;
use ThisData\Api\ResponseManager\AssuredResponseManager;
use ThisData\Api\ResponseManager\ResponseManagerInterface;

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
     * @param string $version
     * @return Builder
     */
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * @param boolean $async
     * @return Builder
     */
    public function setAsync($async)
    {
        $this->async = $async;
        return $this;
    }

    /**
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
