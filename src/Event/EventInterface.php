<?php

namespace ThisData\Api\Event;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;

interface EventInterface
{
    /**
     * @return bool
     */
    public function isSuccessful();

    /**
     * @return Response
     */
    public function getResponse();

    /**
     * @return RequestException
     */
    public function getException();
}
