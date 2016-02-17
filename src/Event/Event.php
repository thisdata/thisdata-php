<?php

namespace ThisData\Api\Event;

use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class Event implements EventInterface
{
    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var RequestException
     */
    private $exception;

    /**
     * @param ResponseInterface|null $response
     * @param RequestException|null $exception
     */
    public function __construct(ResponseInterface $response = null, RequestException $exception = null)
    {
        $this->response  = $response;
        $this->exception = $exception;

        if (null === $response && $exception->hasResponse()) {
            $this->response = $exception->getResponse();
        }
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return ($this->response && !$this->exception)
            && ($this->response->getStatusCode() > 199
                && 300 > $this->response->getStatusCode()
        );
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return RequestException
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @param ResponseInterface $response
     * @return static
     */
    public static function success(ResponseInterface $response)
    {
        return new static($response);
    }

    /**
     * @param RequestException $exception
     * @return static
     */
    public static function error(RequestException $exception)
    {
        return new static($exception->getResponse(), $exception);
    }
}
