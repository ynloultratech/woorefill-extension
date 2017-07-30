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

use WooRefill\App\Api\RefillAPI;
use WooRefill\App\DependencyInjection\CommonServiceTrait;
use WooRefill\Symfony\Component\DependencyInjection\ContainerAwareInterface;

class PhoneContext implements ContainerAwareInterface
{
    use CommonServiceTrait;

    /**
     * @param array $args
     *
     * @return mixed
     */
    public function filterSubcategoriesByPhone($args)
    {
        $phone = $this->getRequest()->get('phone');
        if ($phone) {
            $this->getRequest()->getSession()->set('refill_phone', $phone);
            try {
                $info = $this->getRefillAPI()->accountInfo($phone);
                $wirelessIds = [];
                if ($info && $info->products && !empty($info->products)) {
                    foreach ($info->products as $product) {
                        $wirelessIds[] = $product->id;
                    }
                }

                $categories = [];
                if ($wirelessIds) {
                    foreach ($wirelessIds as $wirelessId) {
                        $productArgs = [
                            'posts_per_page' => -1,
                            'offset' => 0,
                            'post_type' => 'product',
                            'meta_key' => '_wireless_product_id',
                            'meta_value' => $wirelessId,
                        ];
                        $query = new \WP_Query($productArgs);
                        /** @var \WP_Post[] $products */
                        $products = $query->get_posts();
                        foreach ($products as $product) {
                            $productCats = wc_get_product_cat_ids($product->ID);
                            if ($productCats) {
                                $categories = array_merge($categories, $productCats);
                            }
                        }
                    }
                    /** @var \WP_Term[] $children */
                    $children = get_terms($args);
                    $childrenIds = [];
                    foreach ($children as $child) {
                        $childrenIds[] = $child->term_id;
                    }
                    $include = array_intersect($childrenIds, $categories);
                    $args['include'] = implode(',', $include);
                }

            } catch (\Exception $exception) {
                $this->getLogger()->addLog($exception->getMessage());
            }
        }

        return $args;
    }

    /**
     * @param string $link
     *
     * @return string
     */
    public function addPhoneToLink($link)
    {
        $phone = $this->getRequest()->get('phone');
        if ($phone) {
            if (stripos($link, '?') !== false) {
                $link = $link.'&phone='.urlencode($phone);
            } else {
                $link = $link.'?phone='.urlencode($phone);
            }
        }

        return $link;
    }

    /**
     * @param string $phone
     *
     * @return string
     */
    public function resolvePhoneToRefill($phone)
    {
        if (!$phone) {
            $phone = $this->getRequest()->getSession()->get('refill_phone', $phone);
        }

        return $phone;
    }

    /**
     * @return RefillAPI
     */
    protected function getRefillAPI()
    {
        return $this->container->get('refill_api');
    }
}