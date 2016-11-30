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

namespace WooRefill\Shop;

use WooRefill\App\Kernel;
use WooRefill\Tests\PluginTest;

class ThankYouTest extends PluginTest
{
    protected $instance;

    protected function setUp()
    {
        parent::setUp();
        $this->instance = new ThankYou();
        $this->instance->setContainer(Kernel::get('service_container'));
    }

    public function testThankYou()
    {
        self::$functions->shouldReceive('get_post_meta')->with(1)->andReturnValues(['123456789'])->once();

        /** @var ThankYou $thankYou */
        $thankYou = Kernel::get('shop_thankyou');

        ob_start();
        $thankYou->thankYou(1);

        $response = ob_get_clean();
        self::assertContains('<strong>123456789</strong>', $response);
    }
}

