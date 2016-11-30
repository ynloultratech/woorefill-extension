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
use WooRefill\Symfony\Component\DependencyInjection\ContainerInterface;

class PluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MockInterface
     */
    public static $functions;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        self::$functions = \Mockery::mock();
        include_once __DIR__.'/wp-functions.php';
        include_once __DIR__.'/../woorefill.php';
    }

    public function testPluginInitialization()
    {
        /** @var ContainerInterface $container */
        $container = Kernel::get('service_container');
        $logger = $container->get('logger');

        self::assertInstanceOf('WooRefill\App\Logger\Logger', $logger);
        self::assertEquals(APIKEY, $container->getParameter('api_key'));
    }
}
