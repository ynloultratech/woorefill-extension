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
            $fields['refill'] = [
                'phone_to_refill' => [
                    'type' => 'tel',
                    'label' => __('Phone to Refill'),
                    'required' => true,
                ],
            ];
        }

        return $fields;
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
            $note = "Phone to refill with $plan";

            //woo_api_get_fields();

            $phoneInput = woocommerce_form_field(
                'phone_to_refill',
                [
                    'type' => 'tel',
                    'label' => __('Phone to Refill'),
                    'required' => true,
                    'description' => $note,
                ],
                WC()->checkout()->get_value('billing_phone')
            );

            echo <<<HTML
$phoneInput
<hr>
HTML;

        }
    }
);
/**
 * Update order metadata
 */
add_action(
    'woocommerce_checkout_update_order_meta',
    function ($order_id) {
        if (isset($_POST['phone_to_refill']) && wc_cart_has_wireless_product()) {
            update_post_meta($order_id, '_phone_to_refill', $_POST['phone_to_refill']);
        }
    }
);
