<?php

namespace WooRefill\Tests;

use WooRefill\App\Kernel;
use WooRefillMockery\MockInterface;

abstract class AbstractBasePluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MockInterface
     */
    protected static $mockery;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        self::$mockery = \WooRefillMockery::mock();
        include_once __DIR__.'/Fixtures/wp-functions.php';
        include_once __DIR__.'/../woorefill.php';
    }

    /**
     * getMockery
     *
     * @return MockInterface
     */
    public static function getMockery()
    {
        return self::$mockery;
    }

    /**
     * @param string $service #Service
     *
     * @return object
     */
    public function get($service)
    {
        return Kernel::get($service);
    }

    /**
     * @param string $name #Parameter
     *
     * @return object
     */
    public function getParameter($name)
    {
        return Kernel::getParameter($name);
    }
}
