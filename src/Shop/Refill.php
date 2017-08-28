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
use WooRefill\App\DependencyInjection\CommonServiceTrait;
use WooRefill\App\EntityManager\OrderManager;
use WooRefill\App\EntityManager\ProductManager;
use WooRefill\App\Model\Transaction;
use WooRefillSymfony\Component\DependencyInjection\ContainerAwareInterface;

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
        $localProduct = null;
        $product = null;
        $order = null;
        $this->getLogger()->info('Processing Order: '.$id);
        try {
            $order = WC()->order_factory->get_order($id);

            $inProcess = get_post_meta($id, '_woorefill_processing', true);
            if ($inProcess) {
                $this->getLogger()->warning('The order: %s is already in process.', [$id]);

                return;
            }

            $processed = get_post_meta($id, '_woo_api_response_transaction', true);
            if ($processed) {
                $this->getLogger()->info('Ignoring order %s is already processed by WooRefill, transaction: %s', [$id, $processed]);

                return;
            }

            $this->getLogger()->info('Get first wireless product in order %s', [$id]);
            $wcProduct = $this->getOrderManager()->getFirstWirelessProduct($id);
            if ($wcProduct) {
                $this->getLogger()->info('Getting local product ID: %s', [$wcProduct->get_id()]);
                $localProduct = $this->getProductManager()->find($wcProduct->get_id());
                $this->getLogger()->info('Local product match, getting remote product for SKU: %s', [$localProduct->sku]);
                $product = $this->getRefillAPI()->getProducts()->get($localProduct->sku);
                $this->getLogger()->info('Remote product match: %s', [$product->name]);
            } else {
                throw new \Exception('Invalid Order Product');
            }
        } catch (\Exception $exception) {
            $this->getLogger()->error($exception->getMessage());
            if (isset($order)) {
                $this->refundWirelessProduct($order, 'Error: '.$exception->getMessage());
            }
        }

        if ($order && $localProduct && $product) {
            try {
                $transaction = new Transaction();
                $metaArray = get_post_meta($id, null, true);
                $transaction->product = $product;
                $transaction->correlationId = $order->get_id();

                foreach ($metaArray as $metaName => $metaValue) {
                    if (strpos($metaName, '_woo_refill_meta_') !== false) {
                        $metaName = str_replace('_woo_refill_meta_', '', $metaName);
                        if ($metaValue) {
                            $transaction->inputs[$metaName] = current($metaValue);
                        }
                    }
                }

                $this->getLogger()->info('Submitting transaction for order: %s', [$id]);

                update_post_meta($id, '_woorefill_processing', true);
                $transaction = $this->getRefillAPI()->getTransactions()->post($transaction);

                $this->getLogger()->info('Transaction %s completed for order %s, updating order status', [$transaction->id, $id]);

                update_post_meta($id, '_woorefill_processing', false);
                update_post_meta($id, '_woo_api_response_transaction', $transaction->id);
                update_post_meta($id, '_woo_api_response_provider_transaction', $transaction->provider_transaction_id);
                update_post_meta($id, '_woo_api_response_response', $transaction->response_message);
                foreach ($transaction->response as $name => $value) {
                    update_post_meta($id, '_woo_api_response_'.$name, $value);
                }
                $order->update_status('completed', "Refill success \n\n");

            } catch (\Exception $e) {
                update_post_meta($id, '_woorefill_processing', false);
                $this->getLogger()->error($e->getMessage());
                $order->update_status('failed', 'WooRefill: '.$e->getMessage()." \n\n");
                $this->refundWirelessProduct($order, $e->getMessage());
            }
        } else {
            if (isset($wcProduct)) {
                $this->getLogger()->error('The product "%s" does not have valid wireless product id to submit.', [$wcProduct->get_formatted_name()]);
            }
            $order->update_status('cancelled', "Invalid Product \n\n");
            $this->refundWirelessProduct($order, "Invalid Product");
        }
    }

    /**
     * refundWirelessProduct
     *
     * @param \WC_Order $order
     * @param  string   $reason
     *
     * @return bool
     */
    public function refundWirelessProduct(\WC_Order $order, $reason)
    {
        $gateway = wc_get_payment_gateway_by_order($order);
        $amount = $order->calculate_totals();

        if ($gateway instanceof \WC_Payment_Gateway) {
            if ($gateway->supports('refunds')) {
                $refunded = $gateway->process_refund($order->get_id(), $amount, $reason);
                if ($refunded) {
                    wc_create_refund(
                        [
                            'order_id' => $order->get_id(),
                            'amount' => $amount,
                            'reason' => $reason,
                        ]
                    );
                    $order->update_status('refunded');

                    return true;
                }

                $order->add_order_note('The refund has been failed, check your payment gateway logs.');
            } else {
                $gatewayName = $gateway->title;
                $order->add_order_note(sprintf('The gateway %s does not support refund. Make the refund manually', $gatewayName));
            }
        } else {
            $order->add_order_note('The order don`t have a valid payment method to make a refund.');
        }
    }

    /**
     * @return WooRefillApi
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