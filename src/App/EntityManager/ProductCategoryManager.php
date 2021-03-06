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
 * Class ProductCategoryManager
 */
class ProductCategoryManager
{
    /**
     * getProductsCategories
     *
     * @param array $args
     *
     * @return \WP_Term[]
     * @throws \Exception
     */
    public function getProductsCategories($args = [])
    {
        $defaults = [
            'hide_empty' => false,
            'taxonomy' => 'product_cat',
            'meta_query' => [
                'relation' => 'OR',
                [
                    'key' => 'wireless_carrier',
                    'compare' => '=',
                    'value' => null,
                ],
                [
                    'key' => 'wireless_carrier',
                    'compare' => 'NOT EXISTS',
                    'value' => '',
                ],
            ],
        ];
        $args = array_merge($defaults, $args);

        $terms = get_terms($args);;
        if ($terms instanceof \WP_Error) {
            throw new \Exception($terms->get_error_message());
        }

        return $terms;
    }

    /**
     * categoryExists
     *
     * @param       $name
     * @param array $args
     *
     * @return bool
     */
    public function categoryExists($name, $args = [])
    {
        $categories = $this->getProductsCategories($args);
        if (is_array($categories)) {
            foreach ($categories as $category) {
                if ($category->name == $name) {
                    return true;
                }
            }
        }

        return false;
    }
}