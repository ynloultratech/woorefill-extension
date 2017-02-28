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

    public function cartLoadedFromSession(\WC_Cart $cart)
    {
        //updated the product price based on price data
        foreach ($cart->get_cart() as $item) {
            $product = $item['data'];
            if ($product instanceof \WC_Product_Wireless && isset($item['price'])) {
                $product->set_price($item['price']);
            }
        }
    }

    public function addCartItem($cart_item_data)
    {
        //set product price data to use later in the cart, @see cartLoadedFromSession
        $product = $cart_item_data['data'];
        if ($product instanceof \WC_Product_Wireless) {
            $cart_item_data['price'] = $_POST['_wireless_price'];
        }

        return $cart_item_data;
    }

    /**
     * addToCart
     */
    public function addToCart()
    {
        global $product;
        if (!$product->is_purchasable()) {
            return;
        }

        $priceInput = null;
        if ($product instanceof \WC_Product_Wireless && $product->is_variable_price()) {
            $priceInput = [
                'type' => 'number',
                'label' => 'Amount to Refill:',
                'required' => true,
                'custom_attributes' => [
                    'min' => $product->min_price(),
                    'max' => $product->max_price(),
                ],
            ];
        }

        $this->render(
            '@Shop/cart/add_to_cart.html.twig', [
                'product' => $product,
                'price_input' => $priceInput,
            ]
        );
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

            if ($product instanceof \WC_Product_Wireless) {
                $this->removeAllWirelessProducts();
                if ($product->is_variable_price()) {
                    //return false;
                }
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
            if (isset($item['data']) && ($item['data'] instanceof \WC_Product_Wireless)) {
                return $item['data'];
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
        return (boolean) $this->getFirstWirelessProduct();
    }
}