<?php

/**
 * Created by PhpStorm.
 * User: Rafael
 * Date: 29/11/2016
 * Time: 01:39 PM
 */

define('ABSPATH', __DIR__);

define('APIKEY','1234567890ABCDEFGHIJK');

function get_option($key)
{
    $options = [
        '_woorefill_api_key' => APIKEY,
        '_woorefill_log' => 'yes',
    ];

    return $options[$key];
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