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

use WooRefill\App\Model\LocalOperator;
use WooRefill\JMS\Serializer\SerializerBuilder;

/**
 * Class CarrierManager
 */
class OperatorManager
{
    /**
     * @param $id
     *
     * @return null|LocalOperator
     */
    public function find($id)
    {
        $sku = get_term_meta($id, 'wireless_operator_sku', true);

        return $this->findOneBySku($sku);
    }


    /**
     * @param array|integer $skus
     *
     * @return LocalOperator[]
     * @throws \Exception
     */
    public function findAllBySku($skus = null)
    {
        global $wpdb;

        $sql = "SELECT
{$wpdb->terms}.term_id as id,
{$wpdb->terms}.name as name,
{$wpdb->terms}.slug as slug,
metaSku.sku as sku
FROM {$wpdb->terms}
 #Create columns for each meta
 
 # Sku
 LEFT JOIN (SELECT meta_value AS sku, term_id
             FROM {$wpdb->termmeta}
             WHERE meta_key = 'wireless_operator_sku') AS metaSku
    ON {$wpdb->terms}.term_id = metaSku.term_id
     
WHERE metaSku.sku > 0    
";

        if ($skus) {
            $skus = implode(',', (array) $skus);
            $sql .= " AND metaSku.sku IN ($skus)";
        }

        $sql_result = $wpdb->get_results($sql, ARRAY_A);

        $records = [];
        if ($sql_result) {
            $serializer = SerializerBuilder::create()->build();
            $records = $serializer->fromArray($sql_result, 'array<WooRefill\App\Model\LocalOperator>');
        }

        return $records;
    }

    /**
     * @param $sku
     *
     * @return LocalOperator|null
     */
    public function findOneBySku($sku)
    {
        if ($sku) {
            $records = $this->findAllBySku($sku);
            if ($records) {
                return $records[0];
            }
        }

        return null;
    }
}