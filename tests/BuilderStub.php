<?php

namespace ThisData\Api;

class BuilderStub extends Builder
{
    /**
     * @var BuilderTest
     */
    public static $test;

    public function build()
    {
        self::$test->buildFlag = true;
    }

    public static function create($apiKey)
    {
        return parent::create($apiKey);
    }
}
