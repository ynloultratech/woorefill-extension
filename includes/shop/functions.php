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
 * Verify if given product is already in the cart
 *
 * @param $product
 *
 * @return bool
 */
function wc_product_in_cart($product)
{
    $id = $product;
    if ($product instanceof WC_Product) {
        $id = $product->id;
    }
    $cartId = WC()->cart->generate_cart_id($id);

    return (bool)WC()->cart->find_product_in_cart($cartId);
}

/**
 * Verify if the cart as at least one wireless product
 *
 * @return bool
 */
function wc_cart_has_wireless_product()
{
    return (boolean)wc_cart_get_first_wireless_product();
}

/**
 * Clean the current cart of wireless products
 */
function wc_cart_remove_all_wireless_products()
{
    $items = WC()->cart->cart_contents;
    foreach ($items as $key => $item) {
        if (isset($item['product_id']) && $productId = $item['product_id']) {
            $product = wc_get_product($productId);
            if ($product->is_type('wireless')) {
                WC()->cart->remove_cart_item($key);
            }
        }
    }
}

/**
 * Return the first available wireless product in the cart
 *
 * @return null|WC_Product
 */
function wc_cart_get_first_wireless_product()
{
    foreach (WC()->cart->get_cart() as $item) {
        if (isset($item['product_id']) && $productId = $item['product_id']) {
            $product = wc_get_product($productId);
            if ($product->product_type === WC_Product_Wireless::PRODUCT_TYPE_WIRELESS) {
                return $product;
            }
        }
    }

    return null;
}

/**
 * Verify if possible add another wireless product for current cart
 *
 * @return bool
 */
function wc_can_add_another_wireless_product_to_cart()
{
    return allow_buy_multiple_wireless_products() || ! wc_cart_has_wireless_product();
}