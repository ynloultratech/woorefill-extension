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

use WooRefill\App\Api\RefillAPI;
use WooRefill\App\DependencyInjection\CommonServiceTrait;
use WooRefill\App\EntityManager\OrderManager;
use WooRefill\App\EntityManager\ProductManager;
use WooRefill\App\Logger\Logger;
use WooRefill\Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Class Refill
 */
class Refill implements ContainerAwareInterface
{
    use CommonServiceTrait;

    /**
     * refill
     *
     * @param $id
     */
    public function refill($id)
    {
        $order = WC()->order_factory->get_order($id);
        $product = $this->getOrderManager()->getFirstWirelessProduct($id);
        $sku = $this->getProductManager()->getWirelessId($product);

        if ($sku) {
            try {
                $meta = [];
                $metaArray = get_post_meta($id, null, true);
                foreach ($metaArray as $metaName => $metaValue) {
                    if (strpos($metaName, '_woo_refill_meta_') !== false) {
                        $metaName = str_replace('_woo_refill_meta_', '', $metaName);
                        if ($metaValue) {
                            $meta[$metaName] = current($metaValue);
                        }
                    }
                }

                $transaction = $this->getRefillAPI()->submit($sku, $id, $meta);

                update_post_meta($id, '_woo_api_response_transaction', $transaction->id);
                update_post_meta($id, '_woo_api_response_provider_transaction', $transaction->provider_transaction_id);
                update_post_meta($id, '_woo_api_response_response', $transaction->response_message);
                foreach ($transaction->response_meta as $name => $value) {
                    update_post_meta($id, '_woo_api_response_'.$name, $value);
                }
                $order->update_status('completed', "Refill success \n\n");

            } catch (\Exception $e) {
                $this->getLogger()->addLog($e->getTraceAsString());
                $order->update_status('cancelled', $e->getMessage()." \n\n");
            }
        } else {
            $this->getLogger()->addErrorLog('The product "%s" does not have valid wireless product id to submit.', $product->get_formatted_name());
            $order->update_status('cancelled', "Invalid Product \n\n");
        }
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->get('logger');
    }

    /**
     * @return RefillAPI
     */
    public function getRefillAPI()
    {
        return $this->get('refill_api');
    }

    /**
     * @return OrderManager
     */
    public function getOrderManager()
    {
        return $this->get('order_manager');
    }

    /**
     * @return ProductManager
     */
    public function getProductManager()
    {
        return $this->get('product_manager');
    }
}