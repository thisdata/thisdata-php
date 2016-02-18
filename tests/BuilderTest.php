<?php

namespace ThisData\Api;

use ThisData\Api\Event\EventDispatcher;

class BuilderTest extends \PHPUnit_Framework_TestCase
{
    const API_KEY = 'apikey';

    /**
     * @var Builder
     */
    private $builder;

    public $buildFlag = false;

    public function setUp()
    {
        $this->builder = new Builder(self::API_KEY);
    }

    public function testSetVersion()
    {
        $this->assertFluentAttributeChange('version', '1', 'setVersion', '99');
    }

    public function testSetAsync()
    {
        $this->assertFluentAttributeChange('async', true, 'setAsync', false);

        $this->builder->setAsync('badvalue');
        $this->assertAttributeSame(true, 'async', $this->builder);
        $this->builder->setAsync(0);
        $this->assertAttributeSame(false, 'async', $this->builder);
    }

    public function testSetClientOption()
    {
        $result = $this->builder->setClientOption('option', 'value');
        $this->assertInstanceOf(Builder::class, $result);

        $ref = new \ReflectionClass($this->builder);
        $property = $ref->getProperty('clientOptions');
        $property->setAccessible(true);
        $clientOptions = $property->getValue($this->builder);

        $this->assertTrue(array_key_exists('option', $clientOptions));
        $this->assertSame($clientOptions['option'], 'value');
    }

    public function testSetDispatcher()
    {
        $dispatcher = $this->getMock(EventDispatcher::class);

        $this->builder->setDispatcher($dispatcher);
        $this->assertAttributeSame($dispatcher, 'dispatcher', $this->builder);
    }

    public function testBuild()
    {
        $client = $this->builder->build();
        $this->assertInstanceOf(ThisData::class, $client);
    }

    private function assertFluentAttributeChange($attribute, $initial, $setter, $new)
    {
        $this->assertAttributeSame($initial, $attribute, $this->builder);
        $result = $this->builder->$setter($new);
        $this->assertAttributeSame($new, $attribute, $this->builder);
        $this->assertInstanceOf(Builder::class, $result);
    }
}
