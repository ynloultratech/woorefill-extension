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
use WooRefill\Symfony\Component\DependencyInjection\ContainerAwareInterface;

class Shop implements ContainerAwareInterface
{
    use CommonServiceTrait;

    public function preGetPosts(\WP_Query $query)
    {
        if ($this->getRequest()->get('woorefill')) {
            $meta_query = $query->get('meta_query');

            if ($phone = $this->getRequest()->get('phone')) {

                $dialCode = $this->getRequest()->get('dial_code');
                $countryCode = $this->getRequest()->get('country_code');
                $phone = preg_replace('/[^\d]/', '', $phone);
                $phone = preg_replace("/\+?$dialCode/", '', $phone);
                $phone = "+$dialCode$phone";
                $this->getRequest()->getSession()->set('refill_phone', $phone);
                $this->getRequest()->getSession()->set('refill_dial_code', $dialCode);
                $this->getRequest()->getSession()->set('refill_country_code', $countryCode);

                /** @var WooRefillApi $api */
                $api = $this->get('refill_api');
                try {
                    $productIDs = [];
                    $products = $api->getProducts()->getList(null, 1, 30, ['accountNumber' => $phone]);

                    if ($products->total && $products->items) {
                        foreach ($products->items as $product) {
                            $productIDs[] = $product->id;
                        }
                    }
                    $productIDs = array_unique(array_filter($productIDs));
                    if ($productIDs) {

                        global $wpdb;
                        $productIDsStr = implode(',', $productIDs);
                        //find if exist at least one product activated
                        $sql = "SELECT COUNT(meta_value) as count
FROM {$wpdb->postmeta} 
LEFT JOIN {$wpdb->posts} ON post_id =  {$wpdb->posts}.id
WHERE {$wpdb->postmeta}.meta_key = '_wireless_product_id' 
AND {$wpdb->postmeta}.meta_value IN ($productIDsStr) AND {$wpdb->posts}.post_status = 'publish'";

                        $sql_result = $wpdb->get_results($sql, ARRAY_A);

                        //if one product match, filter by this product
                        if (isset($sql_result[0]['count']) && $sql_result[0]['count']) {
                            $meta_query[] = [
                                'key' => '_wireless_product_id',
                                'value' => $productIDs,
                            ];
                        }
                    } else {

                    }

                } catch (\Exception $exception) {
                    //ignore
                }
            }

            if ($countryCode = $this->getRequest()->get('country')) {
                $meta_query[] = [
                    'key' => '_wireless_country_code',
                    'value' => $countryCode,
                ];
            }

            $query->set('meta_query', $meta_query);
        }
    }
}