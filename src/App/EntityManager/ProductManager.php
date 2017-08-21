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

use WooRefill\App\Model\LocalProduct;
use WooRefill\JMS\Serializer\SerializerBuilder;

/**
 * Class ProductManager
 */
class ProductManager
{

    /**
     * @param $id
     *
     * @return null|LocalProduct
     */
    public function find($id)
    {
        $sku = get_post_meta($id, '_wireless_product_id', true);

        return $this->findOneBySku($sku);
    }

    public function findByCarrierId($carrierId)
    {
        global $wpdb;

        $sql = "SELECT
metaSku.sku as sku
FROM {$wpdb->posts}
 
  LEFT JOIN {$wpdb->term_relationships} 
    ON {$wpdb->term_relationships}.object_id = {$wpdb->posts}.ID
    
 # Sku
 LEFT JOIN (SELECT meta_value AS sku, post_id
             FROM {$wpdb->postmeta}
             WHERE meta_key = '_wireless_product_id') AS metaSku
    ON {$wpdb->posts}.ID = metaSku.post_id

WHERE metaSku.sku > 0
   AND {$wpdb->posts}.post_type = 'product'
   AND {$wpdb->term_relationships}.term_taxonomy_id = $carrierId
";
        $sql_result = $wpdb->get_results($sql, ARRAY_A);

        $skus = [];
        foreach ($sql_result as $sku) {
            $skus[] = $sku['sku'];
        }
        if ($skus) {
            return $this->findAllBySku($skus);
        }

        return [];
    }


    /**
     * @param array|integer $skus
     *
     * @return LocalProduct[]
     * @throws \Exception
     */
    public function findAllBySku($skus = null)
    {
        global $wpdb;

        $sql = "SELECT
{$wpdb->posts}.ID as id,
{$wpdb->posts}.post_title as name,
{$wpdb->posts}.post_name as slug,
CASE WHEN ({$wpdb->posts}.post_status = 'publish') THEN 1 ELSE 0 END as enabled,
metaSku.sku as sku
FROM {$wpdb->posts}
 #Create columns for each meta
 
 # Sku
 LEFT JOIN (SELECT meta_value AS sku, post_id
             FROM {$wpdb->postmeta}
             WHERE meta_key = '_wireless_product_id') AS metaSku
    ON {$wpdb->posts}.ID = metaSku.post_id

WHERE metaSku.sku > 0
   AND {$wpdb->posts}.post_type = 'product'
";

        if ($skus) {
            $skus = implode(',', (array) $skus);
            $sql .= " AND metaSku.sku IN ($skus)";
        }

        $sql_result = $wpdb->get_results($sql, ARRAY_A);

        $carriers = [];
        if ($sql_result) {
            $serializer = SerializerBuilder::create()->build();
            $carriers = $serializer->fromArray($sql_result, 'array<WooRefill\App\Model\LocalProduct>');
        }

        return $carriers;
    }

    /**
     * @param $sku
     *
     * @return LocalProduct|null
     */
    public function findOneBySku($sku)
    {
        if ($sku) {
            $products = $this->findAllBySku($sku);
            if ($products) {
                return $products[0];
            }
        }
        return null;
    }

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