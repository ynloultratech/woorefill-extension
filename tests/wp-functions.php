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

define('WP_PLUGIN_DIR', __DIR__);
define('GITHUB_RELEASE_URL', '');

use WooRefill\Tests\AbstractBasePluginTest;

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
    return AbstractBasePluginTest::getMockery()->get_post_meta($id);
}

function add_action($tag)
{
}

function do_action($tag)
{
    return AbstractBasePluginTest::getMockery()->do_action($tag);
}

function add_filter($tag)
{
}

function do_filter($tag)
{
    return AbstractBasePluginTest::getMockery()->do_filter($tag);
}

function plugin_basename()
{
    return call_user_func_array([AbstractBasePluginTest::getMockery(), __FUNCTION__], func_get_args());
}

function get_plugin_data()
{
    return call_user_func_array([AbstractBasePluginTest::getMockery(), __FUNCTION__], func_get_args());
}

function is_plugin_active()
{
    return call_user_func_array([AbstractBasePluginTest::getMockery(), __FUNCTION__], func_get_args());
}

function activate_plugin()
{
    return call_user_func_array([AbstractBasePluginTest::getMockery(), __FUNCTION__], func_get_args());
}

function wp_remote_get()
{
    return call_user_func_array([AbstractBasePluginTest::getMockery(), __FUNCTION__], func_get_args());
}

function wc_get_checkout_url()
{
    return AbstractBasePluginTest::getMockery()->wc_get_checkout_url();
}

class WooCommerce
{

}

class WP_Filesystem_Base
{

    public function move()
    {

    }
}

class WC_Product
{
    /**
     * WC_Product constructor.
     */
    public function __construct($product)
    {
    }

    public function is_purchasable()
    {

    }

    public function is_in_stock()
    {

    }

    public function getId()
    {

    }

}

class WC_Payment_Gateway_CC
{

}