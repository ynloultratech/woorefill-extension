<?php

/*
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 *
 * @author YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package woorefill-extension
 * @version 1.0-alpha
 */

/**
 * Output the simple product add to cart area.
 *
 * @subpackage    Product
 */
add_action(
    'woocommerce_wireless_add_to_cart',
    function () {
        include __DIR__.'/templates/wireless-add-to-cart.php';
    },
    30
);

/**
 * Remove any wireless product before add other to the cart
 */
add_filter(
    'woocommerce_add_to_cart_validation',
    function ($pass, $product_id = null) {
        if ($product_id) {
            $product = wc_get_product($product_id);

            if ($product->is_type('wireless')) {
                wc_cart_remove_all_wireless_products();
            }
        }

        return $pass;
    },
    10,
    2
);

/**
 * Don't show
 */
add_filter(
    'wc_add_to_cart_message',
    function ($pass, $product_id = null) {
        if ($product_id) {
            $product = wc_get_product($product_id);

            if ($product->is_type('wireless')) {
                return null;
            }
        }

        return $pass;
    },
    10,
    2
);

