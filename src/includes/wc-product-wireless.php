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

if ( ! defined('ABSPATH')) {
    exit;
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
        $url = remove_query_arg('added-to-cart', add_query_arg('add-to-cart', $this->id, wc_get_checkout_url()));

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
}