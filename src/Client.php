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

    const HEADER_USER_AGENT   = 'com.thisdata.api/php';
    const HEADER_CONTENT_TYPE = 'application/json';

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var int
     */
    private $version;

    /**
     * @param string $apiKey  The API key for your ThisData account
     * @param int    $version The version of the ThisData API to use
     * @param array  $options Extra options for the GuzzleClient
     */
    public function __construct($apiKey, $version = 1, array $options = [])
    {
        $this->apiKey  = $apiKey;
        $this->version = $version;

        $options = array_merge($this->getDefaultConfiguration(), $options);

        parent::__construct($options);
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
                'User-Agent'   => self::HEADER_USER_AGENT,
                'Content-Type' => self::HEADER_CONTENT_TYPE,
            ]
        ];
    }

    /**
     * @return null|callable
     */
    protected function getHandler()
    {
        return null;
    }

    /**
     * Return the stack of handlers and middlewares responsible for processing
     * requests.
     *
     * @return HandlerStack
     */
    protected function getHandlerStack()
    {
        $handlerStack = HandlerStack::create($this->getHandler());

        $this->configureHandlerStack($handlerStack);

        return $handlerStack;
    }

    /**
     * Add any middleware required.
     *
     * @param HandlerStack $handlerStack
     */
    protected function configureHandlerStack(HandlerStack $handlerStack)
    {
        $handlerStack->unshift($this->getApiKeyMiddleware());
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
}
