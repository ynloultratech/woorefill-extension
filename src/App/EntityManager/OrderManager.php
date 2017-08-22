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
        foreach ($items as $item) {
            if ($item instanceof \WC_Order_Item_Product) {
                $product = wc_get_product($item->get_product_id());
                if ($product instanceof \WC_Product_Wireless) {
                    return $product;
                }
            }
        }

        return null;
    }
}