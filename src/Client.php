<?php

namespace ThisData\Api;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;

/**
 * An extension of the Guzzle HTTP client that lets you communicate with the
 * ThisData REST API.
 */
class Client extends GuzzleClient
{
    const API_PROTOCOL  = 'https';
    const API_HOST      = 'api.thisdata.com';

    const PARAM_API_KEY = 'api_key';

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var int
     */
    private $version;

    /**
     * @param string $apiKey
     * @param int $version
     */
    public function __construct($apiKey, $version = 1)
    {
        $this->apiKey  = $apiKey;
        $this->version = $version;

        parent::__construct($this->getDefaultConfiguration());
    }

    /**
     * Get the configuration for the Guzzle HTTP client.
     *
     * @return array
     */
    protected function getDefaultConfiguration()
    {
        return [
            'handler'  => $this->getHandlerStack(),
            'base_uri' => $this->getBaseUri(),
            'timeout'  => 2,
            'headers'  => [
                'User-Agent' => $this->getUserAgent(),
            ]
        ];
    }

    /**
     * Return the stack of handlers and middlewares responsible for processing
     * requests.
     *
     * @return HandlerStack
     */
    protected function getHandlerStack()
    {
        $handler = HandlerStack::create();

        $handler->unshift($this->getApiKeyMiddleware());

        return $handler;
    }

    /**
     * Return the Guzzle Middleware responsible for ensuring the API key is
     * always present in a request.
     *
     * @return callable
     */
    protected function getApiKeyMiddleware()
    {
        $handleRequest = function (RequestInterface $request) {
            return $request->withUri(Uri::withQueryValue(
                $request->getUri(),
                static::PARAM_API_KEY,
                $this->apiKey
            ));
        };

        return Middleware::mapRequest($handleRequest);
    }

    /**
     * Return the common URI upon which all other endpoints are called.
     *
     * @return string
     */
    protected function getBaseUri()
    {
        return sprintf(
            '%s://%s/v%s/',
            static::API_PROTOCOL,
            static::API_HOST,
            $this->version
        );
    }

    /**
     * Get the value of the User-Agent header to be sent with every API
     * request.
     *
     * @return string
     */
    protected function getUserAgent()
    {
        return 'com.thisdata.api/php';
    }
}
