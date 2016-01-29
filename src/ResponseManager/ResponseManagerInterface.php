<?php

namespace ThisData\Api\ResponseManager;

use GuzzleHttp\Promise\PromiseInterface;

interface ResponseManagerInterface
{
    public function manageResponse(PromiseInterface $promise);
}
