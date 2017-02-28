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
 * Class ProductManager
 */
class ProductManager
{

    /**
     * getWirelessId
     *
     * @param $product
     *
     * @return string|null
     */
    public function getWirelessId($product)
    {
        if ($product instanceof \WC_Product_Wireless) {
            $product = $product->get_id();
        }

        $wirelessId = null;
        if ($product) {
            $wirelessId = get_post_meta($product, '_wireless_product_id', true);
        }

        return $wirelessId;
    }
}