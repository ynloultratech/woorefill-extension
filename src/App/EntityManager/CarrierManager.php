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

/**
 * Class CarrierManager
 */
class CarrierManager
{
    /**
     * getCarriers
     *
     * @param $args
     *
     * @return \WP_Term[]
     * @throws \Exception
     */
    public function getCarriers($args)
    {
        $defaults = [
            'taxonomy' => 'product_cat',
            'meta_key' => 'wireless_carrier',
            'meta_value' => 1,
            'hide_empty' => false,
            'count' => true,
        ];
        $args = array_merge($defaults, $args);

        $terms = get_terms($args);;
        if ($terms instanceof \WP_Error) {
            throw new \Exception($terms->get_error_message());
        }

        return $terms;
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
        $carriers = $this->getCarriers($args);
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