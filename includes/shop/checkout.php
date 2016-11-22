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

/**
 * Resolve fields from api product metadata and add to the list of fields to checkout
 */
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

    }
);

/**
 * Resolve fields from api product metadata and add to the list of fields to checkout
 */
add_action(
    'woocommerce_checkout_billing',
    function () {
        if (wc_cart_has_wireless_product()) {
            $sku = wc_get_wireless_product_sku(wc_cart_get_first_wireless_product());
            $fields = wc_resolve_api_product_fields($sku);
            foreach ($fields as $name => $props) {
                $value = null;

                //phone
                if (preg_match('/_phone$/', $name)) {
                    $value = WC()->checkout()->get_value('billing_phone');
                }

                //amount
                if (preg_match('/_amount$/', $name)) {
                    $value = wc_cart_get_first_wireless_product()->price;
                }

                $input = woocommerce_form_field($name, $props, $value);

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