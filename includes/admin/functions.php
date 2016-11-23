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

/**
 * wr_get_carriers
 *
 * @param array $args
 *
 * @return array|int|WP_Error|WP_Term[]
 */
function wr_get_carriers($args = [])
{
    $defaults = [
        'taxonomy' => 'product_cat',
        'meta_key' => 'wireless_carrier',
        'meta_value' => 1,
        'hide_empty'=>false,
    ];
    $args = array_merge($defaults, $args);

    return get_terms($args);
}

/**
 * wr_get_non_carriers_categories
 *
 * @param array $args
 *
 * @return array|int|WP_Error|WP_Term[]
 */
function wr_get_non_carriers_categories($args = [])
{
    $defaults = [
        'hide_empty'=>false,
        'taxonomy' => 'product_cat',
        'meta_query' => [
            [
                'key' => 'wireless_carrier',
                'compare' => 'NOT EXISTS',
                'value' => '',
            ],
        ],
    ];
    $args = array_merge($defaults, $args);

    return get_terms($args);
}

/**
 * wr_non_carrier_category_exists
 *
 * @param string $name
 * @param array  $args
 *
 * @return boolean
 */
function wr_non_carrier_category_exists($name, $args = [])
{
    $categories = wr_get_non_carriers_categories($args);
    if (is_array($categories)) {
        foreach ($categories as $category) {
            if ($category->name == $name) {
                return true;
            }
        }
    }

    return false;
}

/**
 * wr_carrier_name_exists
 *
 * @param string $name
 * @param array  $args
 *
 * @return boolean
 */
function wr_carrier_name_exists($name, $args = [])
{
    $carriers = wr_get_carriers($args);
    if (is_array($carriers)) {
        foreach ($carriers as $carrier) {
            if ($carrier->name == $name) {
                return true;
            }
        }
    }

    return false;
}