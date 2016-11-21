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

add_action(
    'woocommerce_payment_complete',
    function ($id) {
        $order = WC()->order_factory->get_order($id);
        $product = wc_order_get_wireless_product($id);
        $sku = wc_order_get_wireless_product_sku($id);
        woorefill_log(sprintf('Submitting order %s with product %s', $order->id, $product->get_formatted_name()));

        if ($sku) {
            try {
                $transaction = WooRefillAPI::submit($sku, $id);
                update_post_meta($id, WR_RESPONSE_PREFIX.'transaction', $transaction['id']);
                update_post_meta($id, WR_RESPONSE_PREFIX.'provider_transaction', $transaction['provider_transaction_id']);
                update_post_meta($id, WR_RESPONSE_PREFIX.'response', $transaction['response_message']);
                foreach ($transaction['response_meta'] as $name => $value) {
                    update_post_meta($id, WR_RESPONSE_PREFIX.$name, $value);
                }
                $order->update_status('completed', "Refill success \n\n");

            } catch (\Exception $e) {
                woorefill_log('ERROR: '.$e->getMessage());
                woorefill_log($e);
                $order->update_status('cancelled', $e->getMessage()." \n\n");
            }
        } else {
            woorefill_log('The product does not have valid wireless product id to submit.');
            $order->update_status('cancelled', "Invalid Product \n\n");
        }
    }
);