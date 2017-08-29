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
        '_woorefill_prerelease' => 'no',
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

function update_post_meta()
{
    return call_user_func_array([AbstractBasePluginTest::getMockery(), __FUNCTION__], func_get_args());
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
    public $version = '3.1.0';

    public static function instance()
    {
        return new WooCommerce();
    }
}

function WC()
{
    return WooCommerce::instance();
}

class WP_Filesystem_Base
{

    public function move()
    {

    }
}

class WC_Cart
{

}

class WC_Product
{
    /**
     * WC_Product constructor.
     */
    public function __construct($product)
    {
    }

    public function get_id()
    {
    }

    public function get_type()
    {
    }

    public function get_name($context = 'view')
    {
    }

    public function get_slug($context = 'view')
    {
    }

    public function get_date_created($context = 'view')
    {
    }

    public function get_date_modified($context = 'view')
    {
    }

    public function get_status($context = 'view')
    {
    }

    public function get_featured($context = 'view')
    {
    }

    public function get_catalog_visibility($context = 'view')
    {
    }

    public function get_description($context = 'view')
    {
    }

    public function get_short_description($context = 'view')
    {
    }

    public function get_sku($context = 'view')
    {
    }

    public function get_price($context = 'view')
    {
    }

    public function get_regular_price($context = 'view')
    {
    }

    public function get_sale_price($context = 'view')
    {
    }

    public function get_date_on_sale_from($context = 'view')
    {
    }

    public function get_date_on_sale_to($context = 'view')
    {
    }

    public function get_total_sales($context = 'view')
    {
    }

    public function get_tax_status($context = 'view')
    {
    }

    public function get_tax_class($context = 'view')
    {
    }

    public function get_manage_stock($context = 'view')
    {
    }

    public function get_stock_quantity($context = 'view')
    {
    }

    public function get_stock_status($context = 'view')
    {
    }

    public function get_backorders($context = 'view')
    {
    }

    public function get_sold_individually($context = 'view')
    {
    }

    public function get_weight($context = 'view')
    {
    }


    public function get_length($context = 'view')
    {
    }


    public function get_width($context = 'view')
    {
    }


    public function get_height($context = 'view')
    {
    }


    public function get_dimensions($formatted = true)
    {

    }

    public function get_upsell_ids($context = 'view')
    {
    }

    public function get_cross_sell_ids($context = 'view')
    {
    }

    public function get_parent_id($context = 'view')
    {
    }

    public function get_reviews_allowed($context = 'view')
    {
    }

    public function get_purchase_note($context = 'view')
    {
    }

    public function get_attributes($context = 'view')
    {
    }

    public function get_default_attributes($context = 'view')
    {
    }

    public function get_menu_order($context = 'view')
    {
    }

    public function get_category_ids($context = 'view')
    {
    }

    public function get_tag_ids($context = 'view')
    {
    }

    public function get_virtual($context = 'view')
    {
    }

    public function get_gallery_image_ids($context = 'view')
    {
    }

    public function get_shipping_class_id($context = 'view')
    {
    }

    public function get_downloads($context = 'view')
    {
    }

    public function get_download_expiry($context = 'view')
    {
    }

    public function get_downloadable($context = 'view')
    {
    }

    public function get_download_limit($context = 'view')
    {
    }


    public function get_image_id($context = 'view')
    {
    }


    public function get_rating_counts($context = 'view')
    {
    }


    public function get_average_rating($context = 'view')
    {
    }


    public function get_review_count($context = 'view')
    {
    }

    protected function set_prop($prop, $value)
    {

    }

    public function get_changes()
    {
    }

    public function apply_changes()
    {
    }

    public function is_purchasable()
    {

    }

    public function is_in_stock()
    {
    }

    protected function get_hook_prefix()
    {
    }

    protected function get_prop($prop, $context = 'view')
    {

    }

    protected function set_date_prop($prop, $value)
    {

    }

    protected function error($code, $message, $http_status_code = 400, $data = [])
    {
    }

}

class WC_Payment_Gateway
{

}

class WP_Error
{

}