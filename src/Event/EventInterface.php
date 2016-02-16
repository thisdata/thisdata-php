<?php

namespace ThisData\Api\Event;

use GuzzleHttp\Psr7\Response;

interface EventInterface
{
    /**
     * @return Response
     */
    public function getResponse();
}
