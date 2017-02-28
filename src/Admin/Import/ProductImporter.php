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

namespace WooRefill\Admin\Import;

use WooRefill\App\Api\RefillAPI;
use WooRefill\App\EntityManager\CarrierManager;
use WooRefill\App\EntityManager\ProductCategoryManager;
use WooRefill\Symfony\Component\DependencyInjection\ContainerAwareInterface;
use WooRefill\Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class ProductImporter
 */
class ProductImporter implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param ImportData $importData
     *
     * @throws \Exception
     */
    public function import(ImportData $importData)
    {
        $this->validate($importData);

        $apiProducts = $this->getApi()->getProducts($importData->getCarrierId());
        if (!is_array($apiProducts) || !$apiProducts) {
            throw new \Exception('Unknown error, can\'t import products.');
        }

        if ($importData->isCreateCategory()) {
            $args = [];
            if ($importData->getNewCategoryParent()) {
                $args = ['parent' => $importData->getNewCategoryParent()->term_id];
            }
            $response = wp_insert_term($importData->getNewCategoryName(), 'product_cat', $args);
            if ($response instanceof \WP_Error) {
                throw new \LogicException($response->get_error_message());
            }

            if (is_array($response)) {
                $id = $response['term_id'];
                $importData->setExistentCategory(get_term($id));
            }
        }

        if ($importData->isCreateCarrier()) {
            $response = wp_insert_term($importData->getNewCarrierName(), 'product_cat', ['parent' => $importData->getExistentCategory()->term_id]);
            if ($response instanceof \WP_Error) {
                throw new \LogicException($response->get_error_message());
            }

            if (is_array($response)) {
                $id = $response['term_id'];
                add_term_meta($id, 'wireless_carrier', 1);
                $importData->setExistentCarrier(get_term($id));
            }
        }

        if (is_array($apiProducts)) {
            foreach ($apiProducts as $product) {
                if (in_array($product->id, $importData->getProducts())) {
                    $post_id = wp_insert_post(
                        [
                            'post_title' => $product->name,
                            'post_status' => $importData->getStatus(),
                            'post_type' => "product",
                        ]
                    );
                    wp_set_object_terms($post_id, $importData->getExistentCarrier()->term_id, 'product_cat');
                    wp_set_object_terms($post_id, 'wireless', 'product_type');

                    update_post_meta($post_id, '_wireless_product_id', $product->id);
                    update_post_meta($post_id, '_wireless_variable_price', $product->variable_amount ? 'yes' : 'no');
                    update_post_meta($post_id, '_wireless_min_price', $product->min_amount);
                    update_post_meta($post_id, '_wireless_max_price', $product->max_amount);
                    update_post_meta($post_id, '_wireless_suggested_price', $product->max_amount);
                    update_post_meta($post_id, '_regular_price', $product->amount);
                    update_post_meta($post_id, '_price', $product->amount);
                    update_post_meta($post_id, '_sale_price', $product->amount);
                    update_post_meta($post_id, '_visibility', 'visible');
                    update_post_meta($post_id, '_stock_status', 'instock');
                    update_post_meta($post_id, 'total_sales', '0');
                    update_post_meta($post_id, '_downloadable', 'no');
                    update_post_meta($post_id, '_virtual', 'yes');
                    update_post_meta($post_id, '_sale_price', '');
                    update_post_meta($post_id, '_purchase_note', '');
                    update_post_meta($post_id, '_featured', 'no');
                    update_post_meta($post_id, '_weight', '');
                    update_post_meta($post_id, '_length', '');
                    update_post_meta($post_id, '_width', '');
                    update_post_meta($post_id, '_height', '');
                    update_post_meta($post_id, '_sku', '');
                    update_post_meta($post_id, '_product_attributes', []);
                    update_post_meta($post_id, '_sale_price_dates_from', '');
                    update_post_meta($post_id, '_sale_price_dates_to', '');
                    update_post_meta($post_id, '_sold_individually', '');
                    update_post_meta($post_id, '_manage_stock', 'no');
                    update_post_meta($post_id, '_backorders', 'no');
                    update_post_meta($post_id, '_stock', '');
                }
            }
        }
    }

    /**
     * validate
     */
    protected function validate(ImportData $importData)
    {
        if (!$importData->getProducts()) {
            throw new \LogicException('At least one product is required to import');
        }

        if ($importData->isCreateCarrier() && $this->getCarrierManager()->carrierExists($importData->getNewCarrierName())) {
            throw new \LogicException(sprintf('Already exist a carrier with the name "%s"', $importData->getNewCarrierName()));
        }

        if ($importData->isCreateCategory() && $importData->isCreateCarrier() && $this->getCatManager()->categoryExists($importData->getNewCategoryName())) {
            throw new \LogicException(sprintf('Already exist a category with the name "%s"', $importData->getNewCategoryName()));
        }
    }

    /**
     * @return ProductCategoryManager
     */
    protected function getCatManager()
    {
        return $this->container->get('product_category_manager');
    }

    /**
     * @return CarrierManager
     */
    protected function getCarrierManager()
    {
        return $this->container->get('carrier_manager');
    }

    /**
     * @return RefillAPI
     */
    protected function getApi()
    {
        return $this->container->get('refill_api');
    }
}