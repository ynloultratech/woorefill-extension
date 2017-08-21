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

use WooRefill\App\Model\LocalCarrier;
use WooRefill\JMS\Serializer\SerializerBuilder;

/**
 * Class CarrierManager
 */
class CarrierManager
{

    /**
     * @param $id
     *
     * @return null|LocalCarrier
     */
    public function find($id)
    {
        $sku = get_term_meta($id, 'wireless_carrier_sku', true);

        return $this->findOneBySku($sku);
    }

    public function findByOperatorId($operatorId)
    {
        global $wpdb;

        $sql = "SELECT
metaSku.sku as sku
FROM {$wpdb->terms}
 #Create columns for each meta
 
 LEFT JOIN {$wpdb->term_taxonomy} 
    ON {$wpdb->term_taxonomy}.term_id = {$wpdb->terms}.term_id
 
 # Sku
 LEFT JOIN (SELECT meta_value AS sku, term_id
             FROM {$wpdb->termmeta}
             WHERE meta_key = 'wireless_carrier_sku') AS metaSku
    ON {$wpdb->terms}.term_id = metaSku.term_id
     
WHERE metaSku.sku > 0 
   AND {$wpdb->term_taxonomy}.parent = $operatorId
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
     * getCarriers
     *
     * @param array|integer $skus
     *
     * @return LocalCarrier[]
     * @throws \Exception
     */
    public function findAllBySku($skus = null)
    {
        global $wpdb;

        $sql = "SELECT
{$wpdb->terms}.term_id as id,
{$wpdb->terms}.name as name,
{$wpdb->terms}.slug as slug,
{$wpdb->term_taxonomy}.parent as operatorId,
metaSku.sku as sku
FROM {$wpdb->terms}
 #Create columns for each meta
 
 LEFT JOIN {$wpdb->term_taxonomy} 
    ON {$wpdb->term_taxonomy}.term_id = {$wpdb->terms}.term_id
 
 # Sku
 LEFT JOIN (SELECT meta_value AS sku, term_id
             FROM {$wpdb->termmeta}
             WHERE meta_key = 'wireless_carrier_sku') AS metaSku
    ON {$wpdb->terms}.term_id = metaSku.term_id
    
 # Enabled
 LEFT JOIN (SELECT meta_value AS enabled, term_id
             FROM {$wpdb->termmeta}
             WHERE meta_key = 'wireless_carrier_enabled') AS metaEnabled
    ON {$wpdb->terms}.term_id = metaEnabled.term_id   
     
WHERE metaSku.sku > 0    
";

        if ($skus) {
            $skus = implode(',', (array) $skus);
            $sql .= " AND metaSku.sku IN ($skus)";
        }

        $sql_result = $wpdb->get_results($sql, ARRAY_A);

        $carriers = [];
        if ($sql_result) {
            $serializer = SerializerBuilder::create()->build();
            $carriers = $serializer->fromArray($sql_result, 'array<WooRefill\App\Model\LocalCarrier>');
        }

        return $carriers;
    }

    /**
     * @param $sku
     *
     * @return LocalCarrier|null
     */
    public function findOneBySku($sku)
    {
        if ($sku) {
            $carriers = $this->findAllBySku($sku);
            if ($carriers) {
                return $carriers[0];
            }
        }

        return null;
    }

    /**
     * carrierExists
     *
     * @param       $carrierName
     * @param array $args
     *
     * @return bool
     */
    public function carrierExists($carrierName, $args = [])
    {
        $carriers = $this->findAllBySku($args);
        if (is_array($carriers)) {
            foreach ($carriers as $carrier) {
                if ($carrier->name == $carrierName) {
                    return true;
                }
            }
        }

        return false;
    }
}