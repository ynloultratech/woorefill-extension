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

namespace WooRefill\Test;

use WooRefill\App\Kernel;
use WooRefill\Symfony\Component\DependencyInjection\Container;
use WooRefill\Symfony\Component\DependencyInjection\ContainerInterface;

include 'wp-emulator.php';

class PluginTest extends \PHPUnit_Framework_TestCase
{
    public function testPluginInitialization()
    {
        include __DIR__.'/../woorefill.php';

        /** @var ContainerInterface $container */
        $container = Kernel::get('service_container');
        $logger = $container->get('logger');

        self::assertInstanceOf('WooRefill\App\Logger\Logger', $logger);
        self::assertEquals(APIKEY, $container->getParameter('api_key'));
    }
}
