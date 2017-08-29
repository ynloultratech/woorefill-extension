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

namespace WooRefill\Shop;

use WooRefill\App\Api\WooRefillApi;
use WooRefill\App\Asset\AssetRegister;
use WooRefill\App\DependencyInjection\CommonServiceTrait;
use WooRefill\App\EntityManager\ProductManager;
use WooRefillSymfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Class Checkout
 */
class Checkout implements ContainerAwareInterface
{
    use CommonServiceTrait;

    public function checkoutFields($fields)
    {
        if ($this->getCart()->hasWirelessProduct()) {
            try {
                $product = $this->getCart()->getFirstWirelessProduct();
                $wirelessId = $this->getProductManager()->getWirelessId($product);
                $fields['refill'] = array_merge($fields, $this->resolveAPIProductFields($wirelessId));
            } catch (\Exception $e) {
                //do nothing
            }
        }

        return $fields;
    }

    public function checkoutBilling()
    {
        if ($this->getCart()->hasWirelessProduct()) {
            try {
                /** @var AssetRegister $register */
                $register = $this->container->get('asset_register');
                $register->enqueueStyle('intlTelInput', '/public/int-tel-input/css/intlTelInput.css');
                $register->enqueueScript('intlTelInputUtils', '/public/int-tel-input/js/utils.js');
                $register->enqueueScript('intlTelInput', '/public/int-tel-input/js/intlTelInput.js');
                $register->enqueueScript('inputmask', '/public/js/jquery.inputmask.bundle.js');

                $product = $this->getCart()->getFirstWirelessProduct();
                $apiProduct = $this->getRefillAPI()->getProducts()->get($product->sku);
                $fields = $this->resolveAPIProductFields($product->sku);
                $this->render(
                    '@Shop/checkout/wireless_fields.html.twig',
                    [
                        'fields' => $fields,
                        'product' => $product,
                        'api_product' => $apiProduct,
                    ]
                );
            } catch (\Exception $e) {
                wc_add_notice('This product can\'t be processed now, please try again later.', 'error');
                $this->getCart()->removeAllWirelessProducts();
                wp_redirect(wc_get_cart_url());
            }
        }
    }

    /**
     * This method is called when checkout is submitted to save
     * order wireless metadata like phone number, amount to refill etc
     *
     * @param $order_id
     */
    public function updateOrderMeta($order_id)
    {
        if ($this->getCart()->hasWirelessProduct()) {
            foreach ($_POST as $field => $value) {
                if (strpos($field, '_woo_refill_meta_') !== false) {
                    if (preg_match('/meta_phone$/', $field)) {
                        //remove the mask, keep phone number and int prefix,
                        //+(1) 3051234123 -> +13051234123
                        $value = preg_replace('/[^\+\d]/', '', $value);
                    }
                    update_post_meta($order_id, $field, $value);
                }
            }
        }
    }

    /**
     * Validates the posted checkout data based on field properties.
     *
     * @param  array     $data An array of posted data.
     * @param  \WP_Error $errors
     */
    public function validatePostedData($data, $errors)
    {
        $product = $this->getCart()->getFirstWirelessProduct();
        $sku = $this->getProductManager()->find($product->get_id())->sku;
        $product = $this->getRefillAPI()->getProducts()->get($sku);

        foreach ($product->inputs as $name => $props) {
            $required = (boolean) @$props['required'];
            $dataKey = sprintf('_woo_refill_meta_%s', $name);
            $value = @$data[$dataKey];

            if ($required && !$value) {
                $errors->add('validation', sprintf('<strong>%s</strong> is a required field', $props['label']));
            }

            if ($regexRules = @$props['validationRegex']) {
                foreach ($regexRules as $rule => $label) {
                    try {
                        if (!preg_match($rule, $value)) {
                            $errors->add('validation', $label);
                        }
                    } catch (\Exception $exception) {
                        $this->getLogger()->error('Validation expression error: '.$exception->getMessage());
                    }
                }
            }
        }
    }

    public function resolveAPIProductFields($wirelessId)
    {
        $product = $this->getRefillAPI()->getProducts()->get($wirelessId);

        $fields = [];
        if ($product->inputs) {
            foreach ($product->inputs as $name => $prop) {

                $value = null;
                if ($name === 'phone') {
                    $phone = apply_filters('woorefill_default_phone_to_refill', null, $product);
                    if (strpos($phone, '+'.$product->carrier->internationalCode) === false) {
                        $phone = str_replace('+', '+'.$product->carrier->internationalCode, $phone);
                    }

                    $value = $phone;
                }

                $fields[sprintf('_woo_refill_meta_%s', $name)] = [
                    'type' => @$prop['inputType'] ?: 'text',
                    'label' => @$prop['label'] ?: ucfirst($name),
                    'required' => @$prop['required'] ?: false,
                    'value' => $value,
                    'custom_attributes' => [
                        'data-country' => $product->carrier->countryCode,
                    ],
                ];

                //hide amount, is already settled in the addToCart
                if ($name === 'amount') {
                    $fields[sprintf('_woo_refill_meta_%s', $name)]['type'] = 'hidden';
                    $fields[sprintf('_woo_refill_meta_%s', $name)]['label'] = null;
                    $fields[sprintf('_woo_refill_meta_%s', $name)]['required'] = false;
                }
            }
        }

        return $fields;
    }

    /**
     * @return WooRefillApi
     */
    protected function getRefillAPI()
    {
        return $this->container->get('refill_api');
    }

    /**
     * @return Cart
     */
    protected function getCart()
    {
        return $this->container->get('shop_cart');
    }

    /**
     * @return ProductManager
     */
    protected function getProductManager()
    {
        return $this->container->get('product_manager');
    }
}