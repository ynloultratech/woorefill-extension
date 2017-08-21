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

namespace WooRefill\Admin\Controller;

use WooRefill\App\Api\WooRefillApi;
use WooRefill\App\Controller\Controller;
use WooRefill\App\EntityManager\CarrierManager;
use WooRefill\App\EntityManager\OperatorManager;
use WooRefill\App\EntityManager\ProductManager;
use WooRefill\App\Model\Carrier;
use WooRefill\App\Model\LocalCarrier;
use WooRefill\App\Model\LocalOperator;
use WooRefill\App\Model\LocalProduct;
use WooRefill\App\Model\Operator;
use WooRefill\App\Model\Product;

class WirelessProducts extends Controller
{
    public function wirelessProductsAction()
    {
        $page = $this->getRequest()->get('pagenum', 1);
        $search = $this->getRequest()->get('q');
        $collection = $this->getApi()->getCarriers()->getList($search, $page);

        $this->render(
            '@Admin/products/wireless_products.html.twig',
            [
                'collection' => $collection,
                'search' => $search,
            ]
        );
    }

    public function productsAction()
    {
        $carrierId = $this->getRequest()->get('carrierId', 0);
        $collection = $this->getApi()->getProducts()->getList(
            null,
            1,
            100,
            [
                'carrierId' => $carrierId,
            ]
        );

        $this->renderAjax(
            '@Admin/products/details.html.twig',
            [
                'products' => $collection->items,
                'carrierId' => $carrierId,
            ]
        );
    }

    public function switchCarrierAction()
    {
        $enabled = false;

        try {
            $sku = $this->getRequest()->get('sku');
            $carrier = $this->getApi()->getCarriers()->get($sku);

            if ($carrier) {
                $enabled = $this->switchCarrier($carrier);
                $this->switchProductsForCarrier($carrier, $enabled);
            }
        } catch (\Exception $exception) {
            die($exception->getMessage());
        }

        $this->renderJson(['status' => $enabled ? 'enabled' : 'disabled']);
    }

    public function switchProductAction()
    {
        $enabled = false;
        $carrierStatus = false;

        try {
            $sku = $this->getRequest()->get('sku');
            $product = $this->getApi()->getProducts()->get($sku);

            if ($product) {
                $enabled = $this->switchProduct($product);
                $localCarrier = $this->fetchOrCreateLocalCarrier($product->carrier);

                $disableCarrier = true;
                $relatedProducts = $this->getProductManager()->findByCarrierId($localCarrier->id);
                foreach ($relatedProducts as $relatedProduct) {
                    if ($relatedProduct->enabled) {
                        $disableCarrier = false;
                    }
                }
                if ($disableCarrier) {
                    $this->switchCarrier($product->carrier, false);
                }

                $carrierStatus = !$disableCarrier;
            }
        } catch (\Exception $exception) {
            die($exception->getMessage());
        }

        $this->renderJson(
            [
                'status' => $enabled ? 'enabled' : 'disabled',
                'carrier_status' => $carrierStatus ? 'enabled' : 'disabled',
            ]
        );
    }

    /**
     * @param Product      $product
     * @param boolean|null $status status tu set or null to set based on current
     *
     * @return bool enable status
     */
    protected function switchProduct(Product $product, $status = null)
    {
        $localProduct = $this->fetchOrCreateLocalProduct($product);

        if ($status === null) {
            $status = !$localProduct->enabled;
        }

        if ($status) {
            wp_publish_post($localProduct->id);
            update_post_meta($localProduct->id, '_wireless_product_enabled', 1);

            $localCarrier = $this->fetchOrCreateLocalCarrier($product->carrier);
            $localOperator = $this->fetchOrCreateLocalOperator($product->carrier->operator);

            $currentLogo = get_post_meta($localCarrier->id, '_thumbnail_id', true);
            if (!$currentLogo || !get_post($currentLogo)) {
                if ($logoId = get_term_meta($localCarrier->id, 'thumbnail_id', true)) {
                    update_post_meta($localProduct->id, '_thumbnail_id', $logoId);
                }
            }

            wp_set_object_terms($localProduct->id, [$localOperator->id, $localCarrier->id], 'product_cat');
        } else {
            global $wpdb;
            $wpdb->update($wpdb->posts, ['post_status' => 'trash'], ['ID' => $localProduct->id]);
            update_post_meta($localProduct->id, '_wireless_product_enabled', 0);
        }

        return $status;
    }

    /**
     * @param Carrier      $carrier
     * @param boolean|null $status status tu set or null to set based on current
     *
     * @return bool enable status
     */
    protected function switchCarrier(Carrier $carrier, $status = null)
    {
        $localCarrier = $this->getCarrierManager()->findOneBySku($carrier->id);

        if ($status === null) {
            $status = !(boolean) $localCarrier;
        }

        if (!$status) {
            wp_delete_term($localCarrier->id, 'product_cat');

            $carriers = $this->getCarrierManager()->findByOperatorId($localCarrier->operatorId);
            $removeEmptyOperator = true;
            if ($carriers) {
                foreach ($carriers as $carrier) {
                    if ($carrier->enabled) {
                        $removeEmptyOperator = false;
                        break;
                    }
                }
            }
            if ($removeEmptyOperator) {
                $logoId = get_term_meta($localCarrier->operatorId, 'thumbnail_id', true);
                if ($logoId) {
                    wp_delete_post($logoId);
                }
                wp_delete_term($localCarrier->operatorId, 'product_cat');
            }
        } else {
            $this->fetchOrCreateLocalCarrier($carrier);
        }

        return $status;
    }

    /**
     * @param Carrier $carrier
     * @param boolean $enabled
     */
    protected function switchProductsForCarrier(Carrier $carrier, $enabled)
    {
        $products = $this->getApi()->getProducts()->getList(null, 1, 100, ['carrierId' => $carrier->id]);
        foreach ($products->items as $product) {
            $this->switchProduct($product, $enabled);
        }
        wp_update_term_count($carrier->localCarrier->id, 'product_cat');
    }

    /**
     * Get or create local operator term
     *
     * @param Operator $operator
     *
     * @return LocalOperator
     */
    protected function fetchOrCreateLocalOperator(Operator $operator)
    {
        //resolve existent operator
        if ($localOperator = $this->getOperatorManager()->findOneBySku($operator->id)) {
            return $localOperator;
        }

        //create new one
        $response = wp_insert_term($operator->name, 'product_cat');
        if ($response instanceof \WP_Error) {
            throw new \LogicException($response->get_error_message());
        }

        $id = $response['term_id'];
        add_term_meta($id, 'wireless_operator_sku', $operator->id);

        $logoId = get_term_meta($id, 'thumbnail_id', true);

        //verify the logo exist
        if (!get_post($logoId)) {
            $logoId = null;
        }

        if (!$logoId && $operator->logoUrl) {
            $logoId = wp_insert_attachment_from_url(
                $operator->logoUrl,
                [
                    'post_title' => $operator->name.' (logo)',
                    'post_content' => '',
                    'post_excerpt' => $operator->name,
                ]
            );
            update_post_meta($logoId, '_wp_attachment_image_alt', $operator->name);
            update_post_meta($logoId, '_woorefill_logo', $operator->logoUrl);
            update_term_meta($id, 'thumbnail_id', $logoId);
        }

        return $this->getOperatorManager()->find($id);
    }

    /**
     * @param Carrier $carrier
     *
     * @return LocalCarrier
     */
    protected function fetchOrCreateLocalCarrier(Carrier $carrier)
    {
        //check before and return existent instance
        if ($localCarrier = $this->getCarrierManager()->findOneBySku($carrier->id)) {
            return $localCarrier;
        }

        $localOperator = $this->getOperatorManager()->findOneBySku($carrier->operator->id);
        if (!$localOperator) {
            $localOperator = $this->fetchOrCreateLocalOperator($carrier->operator);
        }

        //insert carrier
        $response = wp_insert_term($carrier->name, 'product_cat', ['parent' => $localOperator->id]);

        if ($response instanceof \WP_Error) {
            throw new \LogicException($response->get_error_message());
        }

        $id = $response['term_id'];
        add_term_meta($id, 'wireless_carrier_sku', $carrier->id);

        if ($logoId = get_term_meta($localOperator->id, 'thumbnail_id', true)) {
            update_term_meta($id, 'thumbnail_id', $logoId);
        }

        return $this->getCarrierManager()->find($id);
    }

    /**
     * @param Product $product
     *
     * @return LocalProduct
     */
    protected function fetchOrCreateLocalProduct(Product $product)
    {
        //check before and return existent instance
        if ($localProduct = $this->getProductManager()->findOneBySku($product->id)) {
            return $localProduct;
        }

        $localCarrier = $this->fetchOrCreateLocalCarrier($product->carrier);
        $localOperator = $this->fetchOrCreateLocalOperator($product->carrier->operator);
        $carrier = $this->getApi()->getCarriers()->get($localCarrier->sku);

        $post_id = wp_insert_post(
            [
                'post_title' => $product->name,
                'post_status' => 'publish',
                'post_type' => "product",
            ]
        );
        wp_set_object_terms($post_id, [$localOperator->id, $localCarrier->id], 'product_cat');
        wp_set_object_terms($post_id, 'wireless', 'product_type');

        update_post_meta($post_id, '_wireless_product_id', $product->id);
        update_post_meta($post_id, '_wireless_product_enabled', 0);
        update_post_meta($post_id, '_wireless_variable_price', $product->variableAmount ? 'yes' : 'no');
        update_post_meta($post_id, '_wireless_min_price', $product->minAmount);
        update_post_meta($post_id, '_wireless_max_price', $product->maxAmount);
        update_post_meta($post_id, '_wireless_suggested_price', null);
        update_post_meta($post_id, '_wireless_type', $product->type);
        update_post_meta($post_id, '_wireless_discount_rate', $product->discountRate);
        update_post_meta($post_id, '_wireless_currency_code', $carrier->currencyCode);
        update_post_meta($post_id, '_wireless_country_code', $carrier->countryCode);
        update_post_meta($post_id, '_wireless_international_code', $carrier->internationalCode);
        update_post_meta($post_id, '_wireless_phone_length', $carrier->phoneLength);
        update_post_meta($post_id, '_regular_price', $product->amount);
        update_post_meta($post_id, '_price', $product->amount);
        update_post_meta($post_id, '_sale_price', $product->amount);
        update_post_meta($post_id, '_visibility', 'visible');
        update_post_meta($post_id, '_stock_status', 'instock');
        update_post_meta($post_id, 'total_sales', '0');
        update_post_meta($post_id, '_downloadable', 'no');
        update_post_meta($post_id, '_virtual', 'yes');
        update_post_meta($post_id, '_sale_price', $product->amount);
        update_post_meta($post_id, '_purchase_note', '');
        update_post_meta($post_id, '_featured', 'no');
        update_post_meta($post_id, '_weight', '');
        update_post_meta($post_id, '_length', '');
        update_post_meta($post_id, '_width', '');
        update_post_meta($post_id, '_height', '');
        update_post_meta($post_id, '_sku', $product->id);
        update_post_meta($post_id, '_product_attributes', []);
        update_post_meta($post_id, '_sale_price_dates_from', '');
        update_post_meta($post_id, '_sale_price_dates_to', '');
        update_post_meta($post_id, '_sold_individually', '');
        update_post_meta($post_id, '_manage_stock', 'no');
        update_post_meta($post_id, '_backorders', 'no');
        update_post_meta($post_id, '_stock', '');

        if ($logoId = get_term_meta($localCarrier->id, 'thumbnail_id', true)) {
            update_post_meta($post_id, '_thumbnail_id', $logoId);
        }

        return $this->getProductManager()->find($post_id);
    }

    /**
     * @return ProductManager|object
     */
    public function getProductManager()
    {
        return $this->container->get('product_manager');
    }

    /**
     * @return CarrierManager|object
     */
    public function getCarrierManager()
    {
        return $this->container->get('carrier_manager');
    }

    /**
     * @return OperatorManager|object
     */
    public function getOperatorManager()
    {
        return $this->container->get('operator_manager');
    }

    /**
     * @return WooRefillApi|object
     */
    protected function getApi()
    {
        return $this->container->get('refill_api');
    }
}