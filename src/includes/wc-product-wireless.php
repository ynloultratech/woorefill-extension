<?php

/*
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 *
 * @author YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package woorefill-extension
 * @version 1.0.x
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WC_Product')) {
    include WC()->plugin_path().DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, ['includes', 'abstracts', 'abstract-wc-product.php']);
}

/**
 * WC_Product_Wireless
 *
 * WooCommerce check for a class called WC_Product_{product_type}
 * otherwise use the common WC_Product_Simple
 */
class WC_Product_Wireless extends WC_Product
{
    const PRODUCT_TYPE_WIRELESS = 'wireless';

    /**
     * @param mixed $product
     */
    public function __construct($product)
    {
        parent::__construct($product);

        $this->product_type = self::PRODUCT_TYPE_WIRELESS;
    }

    /**
     * Get the add to url used mainly in loops.
     *
     * @return string
     */
    public function add_to_cart_url()
    {
        if ($this->is_variable_price()) {
            return parent::add_to_cart_url();
        }

        $url = remove_query_arg('added-to-cart', add_query_arg('add-to-cart', $this->get_id(), wc_get_checkout_url()));

        return apply_filters('woocommerce_product_add_to_cart_url', $url, $this);
    }

    /**
     * Get the add to cart button text.
     *
     * @return string
     */
    public function add_to_cart_text()
    {
        $text = __('Refill');

        return apply_filters('woocommerce_product_add_to_cart_text', $text, $this);
    }

    public function is_variable_price()
    {
        return (strtolower($this->get_meta('_wireless_variable_price', true)) === 'yes');
    }

    public function min_price()
    {
        return $this->get_meta('_wireless_min_price', true);
    }

    public function max_price()
    {
        return $this->get_meta('_wireless_max_price', true);
    }

    public function suggested_price()
    {
        return $this->get_meta('_wireless_suggested_price', true);
    }

    /**
     * Returns the price in html format.
     *
     * @param string $price (default: '')
     *
     * @return string
     */
    public function get_price_html($price = '')
    {
        if ($this->is_variable_price()) {
            $price = wc_price($this->min_price()).' - '.wc_price($this->max_price());

            return apply_filters('woocommerce_sale_price_html', $price, $this);
        }

        return parent::get_price_html($price);
    }
}