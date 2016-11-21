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

if ( ! defined('ABSPATH')) {
    exit;
}

add_filter(
    'woocommerce_checkout_fields',
    function ($fields) {
        if (wc_cart_has_wireless_product()) {
            $sku = wc_get_wireless_product_sku(wc_cart_get_first_wireless_product());
            $fields = array_merge($fields, wc_resolve_api_product_fields($sku));
        }

        return $fields;
    }
);

add_action(
    'woocommerce_checkout_process',
    function () {
        //wc_add_notice( __( 'You must accept our Terms &amp; Conditions.', 'woocommerce' ), 'error' );
    }
);

/**
 * Add phone to refill
 */
add_action(
    'woocommerce_checkout_billing',
    function () {
        if (wc_cart_has_wireless_product()) {
            $plan = wc_cart_get_first_wireless_product()->get_title();
            $sku = wc_get_wireless_product_sku(wc_cart_get_first_wireless_product());
            $fields = wc_resolve_api_product_fields($sku);
            foreach ($fields as $name => $props) {
                // WC()->checkout()->get_value('billing_phone')
                //$note = "Phone to refill with $plan";
                $input = woocommerce_form_field($name, $props);

                echo $input;
            }
        }
    }
);

/**
 * Update order metadata
 */
add_action(
    'woocommerce_checkout_update_order_meta',
    function ($order_id) {
        if (wc_cart_has_wireless_product()) {
            foreach ($_POST as $field => $value) {
                if (strpos($field, '_woo_refill_meta_') !== false) {
                    update_post_meta($order_id, $field, $value);
                }
            }
        }
    }
);
