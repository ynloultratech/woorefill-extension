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

namespace WooRefill\App\EntityManager;

/**
 * Class OrderManager
 */
class OrderManager
{
    /**
     * Get first available wireless product in given order id
     *
     * @param int|\WC_Order $order
     *
     * @return null|\WC_Product
     */
    public function getFirstWirelessProduct($order)
    {
        if (!$order instanceof \WC_Order) {
            $order = WC()->order_factory->get_order($order);
        }

        $items = $order->get_items();
        $product = null;
        foreach ($items as $item) {
            if (isset($item['item_meta']['_product_id'])) {
                if (is_array($item['item_meta']['_product_id'])) {
                    $productId = current($item['item_meta']['_product_id']);
                } else {
                    $productId = $item['item_meta']['_product_id'];
                }

                $product = wc_get_product($productId);
                if ($product instanceof \WC_Product_Wireless) {
                    break;
                }
            }
        }

        return $product;
    }
}