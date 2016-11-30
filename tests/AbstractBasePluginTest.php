<?php

/*
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 *
 * @author YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package woorefill-extension
 * @version 1.0.x
 */

namespace WooRefill\Tests;

use Mockery\MockInterface;
use WooRefill\App\Kernel;

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
        self::$mockery = \Mockery::mock();
        include_once __DIR__.'/wp-functions.php';
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
     * @param string $service
     *
     * @return object
     */
    public function get($service)
    {
        return Kernel::get($service);
    }

    /**
     * @param string $name
     *
     * @return object
     */
    public function getParameter($name)
    {
        return Kernel::getParameter($name);
    }
}
