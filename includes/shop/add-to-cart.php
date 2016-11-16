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

add_filter(
    'woocommerce_loop_add_to_cart_link',
    function ($link) {
        global $product;
        if ($product->is_type('wireless')) {
            if (wc_product_in_cart($product) || ! wc_can_add_another_wireless_product_to_cart()) {
                return null;
            }
        }

        return $link;
    }
);
