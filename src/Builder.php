<?php

namespace ThisData\Api;

use ThisData\Api\Event\EventDispatcher;
use ThisData\Api\Event\EventDispatcherInterface;
use ThisData\Api\Event\EventInterface;
use ThisData\Api\RequestHandler\AsynchronousRequestHandler;
use ThisData\Api\RequestHandler\RequestHandlerInterface;
use ThisData\Api\RequestHandler\SynchronousRequestHandler;

/**
 * Builds an instance of ThisData.
 *
 * Given an API key from ThisData, this class provides a fluent programmatic
 * interface for building an abstracted client capable of interacting with
 * the ThisData API.
 */
class Builder
{
    // Some configuration options are passed around in an array, using these
    // keys
    const CONF_EXPECT_JS_COOKIE  = 'expectJsCookie';

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
     * @var bool
     */
    private $expectJsCookie = false;

    /**
     * @var array
     */
    private $clientOptions = [];

    /**
     * @var RequestHandlerInterface
     */
    private $requestHandler;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

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
        $this->async = (bool)$async;
        return $this;
    }

    /**
     * ThisData's JS library (optional) adds a cookie.
     * If you're using the library, set this to true, so that we know to expect
     * a cookie value
     *
     * @param boolean $async
     * @return Builder
     */
    public function setExpectJsCookie($expectJsCookie)
    {
        $this->expectJsCookie = (bool)$expectJsCookie;
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
     * @param RequestHandlerInterface $requestHandler
     */
    public function setRequestHandler(RequestHandlerInterface $requestHandler)
    {
        $this->requestHandler = $requestHandler;
    }

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
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
     * @return EventDispatcherInterface
     */
    protected function getDispatcher()
    {
        if (null === $this->dispatcher) {
            $this->dispatcher = $this->buildDispatcher();
        }

        return $this->dispatcher;
    }

    /**
     * @return RequestHandlerInterface
     */
    protected function buildRequestHandler()
    {
        $dispatcher = $this->getDispatcher();

        switch ($this->async) {
            case false:
                return new SynchronousRequestHandler($dispatcher);
            case true:
            default:
                return new AsynchronousRequestHandler($dispatcher);
        }
    }

    /**
     * @return EventDispatcher
     */
    protected function buildDispatcher()
    {
        $dispatcher = new EventDispatcher();

        $dispatcher->listen(EventDispatcherInterface::EVENT_REQUEST_ERROR, function (EventInterface $event) {
            error_log($event->getException()->getMessage());
        });

        return $dispatcher;
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
        // Pass through any configuration options set here which need to be made
        // available elsewhere in the ThisData instance.
        $configuration  = [
            self::CONF_EXPECT_JS_COOKIE => $this->expectJsCookie
        ];

        return new ThisData($client, $requestHandler, $configuration);
    }
}
