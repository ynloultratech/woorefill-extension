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

namespace WooRefill\Shop;

use WooRefill\App\DependencyInjection\CommonServiceTrait;
use WooRefill\Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Class Cart
 */
class Cart implements ContainerAwareInterface
{
    use CommonServiceTrait;

    /**
     * addToCart
     */
    public function addToCart()
    {
        global $product;
        if (!$product->is_purchasable()) {
            return;
        }

        $this->render('@Shop/cart/add_to_cart.html.twig', ['product' => $product]);
    }

    /**
     * addToCartValidate
     *
     * @param      $pass
     * @param null $product_id
     *
     * @return mixed
     */
    public function addToCartValidate($pass, $product_id = null)
    {
        if ($product_id) {
            $product = wc_get_product($product_id);

            if ($product->is_type('wireless')) {
                $this->removeAllWirelessProducts();
            }
        }

        return $pass;
    }

    /**
     * addToCartMessage
     *
     * @param      $pass
     * @param null $product_id
     *
     * @return null
     */
    public function addToCartMessage($pass, $product_id = null)
    {
        if ($product_id) {
            $product = wc_get_product($product_id);

            if ($product->is_type('wireless')) {
                return null;
            }
        }

        return $pass;
    }

    /**
     * removeAllWirelessProducts
     */
    public function removeAllWirelessProducts()
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
     * getFirstWirelessProduct
     *
     * @return null|\WC_Product
     */
    public function getFirstWirelessProduct()
    {
        foreach (WC()->cart->get_cart() as $item) {
            if (isset($item['product_id']) && $productId = $item['product_id']) {
                $product = wc_get_product($productId);
                if ($product->product_type === \WC_Product_Wireless::PRODUCT_TYPE_WIRELESS) {
                    return $product;
                }
            }
        }

        return null;
    }

    /**
     * hasWirelessProduct
     *
     * @return bool
     */
    public function hasWirelessProduct()
    {
        return (boolean)$this->getFirstWirelessProduct();
    }
}