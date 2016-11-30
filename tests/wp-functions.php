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

define('ABSPATH', __DIR__);

define('APIKEY', '1234567890ABCDEFGHIJK');

use WooRefill\Tests\PluginTest;

function get_option($key)
{
    $options = [
        '_woorefill_api_key' => APIKEY,
        '_woorefill_log' => 'yes',
    ];

    return $options[$key];
}

function get_post_meta($id)
{
    return PluginTest::$functions->get_post_meta($id);
}

function add_action()
{

}

function add_filter()
{

}

class WooCommerce
{

}

class WC_Product
{

}

class WC_Payment_Gateway_CC
{

}