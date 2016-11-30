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

namespace WooRefill\Tests\Shop;

use WooRefill\Shop\ThankYou;
use WooRefill\Tests\AbstractBasePluginTest;

class ThankYouTestAbstractBase extends AbstractBasePluginTest
{
    public function testThankYou()
    {
        self::getMockery()->shouldReceive('get_post_meta')->with(1)->andReturnValues(['123456789'])->once();

        /** @var ThankYou $thankYou */
        $thankYou = $this->get('shop_thankyou');

        ob_start();
        $thankYou->thankYou(1);

        $response = ob_get_clean();
        self::assertContains('<strong>123456789</strong>', $response);
    }
}

