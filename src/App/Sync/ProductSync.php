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

namespace WooRefill\App\Sync;

use WooRefill\App\Api\WooRefillApi;
use WooRefill\App\DependencyInjection\CommonServiceTrait;
use WooRefill\App\EntityManager\CarrierManager;
use WooRefill\App\EntityManager\ProductManager;
use WooRefill\App\Model\Product;
use WooRefillSymfony\Component\DependencyInjection\ContainerAwareInterface;

class ProductSync implements ContainerAwareInterface
{
    use CommonServiceTrait;

    protected $discontinuedSuffix = '[discontinued]';

    protected $products = [];

    protected $carriers = [];

    public function sync()
    {
        $currentPage = 0;
        $totalPages = 1;
        $products = [];
        $carriers = [];

        while ($currentPage < $totalPages) {
            $currentPage++;
            $pagination = $this->getRefillAPI()->getProducts()->getList(null, $currentPage, 100);
            $totalPages = $pagination->pages;
            set_transient('_woorefill_products_synchronized_' + $currentPage, count($pagination->items));
            if ($pagination->items && $pagination->page === $currentPage) {
                /** @var Product $item */
                foreach ($pagination->items as $item) {
                    $products[$item->id] = [
                        'name' => $item->name,
                        'amount' => $item->amount,
                        'minAmount' => $item->minAmount,
                        'maxAmount' => $item->maxAmount,
                        'type' => $item->type,
                        'variableAmount' => $item->variableAmount,
                        'carrierId' => $item->carrier->id,
                        'logoUrl' => $item->carrier->operator->logoUrl,
                    ];

                    $carriers[$item->carrier->id] = [
                        'name' => $item->carrier->name,
                        'countryCode' => $item->carrier->countryCode,
                    ];
                }
            }
        }

        if ($products && $carriers) {
            global $wpdb;

            $sql = "SELECT
{$wpdb->posts}.ID as id,
metaSku.sku as sku
FROM {$wpdb->posts}
    
# Sku
LEFT JOIN (SELECT meta_value AS sku, post_id
             FROM {$wpdb->postmeta}
             WHERE meta_key = '_wireless_product_id') AS metaSku
    ON {$wpdb->posts}.ID = metaSku.post_id

WHERE metaSku.sku > 0 AND {$wpdb->posts}.post_type = 'product'
";

            $activatedProducts = $wpdb->get_results($sql, ARRAY_A);
            $productSkus = array_keys($products);
            foreach ($activatedProducts as $activatedProduct) {
                $product = wc_get_product($activatedProduct['id']);
                $remoteProductData = $products[$activatedProduct['sku']];

                if (in_array($activatedProduct['sku'], $productSkus)) { //publish
                    if (strpos($product->get_name(), $this->discontinuedSuffix) !== false) {
                        $newName = str_replace(' '.$this->discontinuedSuffix, null, $product->get_name());
                        $product->set_name($newName);
                    }
                    $product->set_status('publish');
                    $product->set_regular_price($remoteProductData['amount']);
                    $product->save();

                    $localCarrier = $this->getCarrierManager()->findOneBySku($remoteProductData['carrierId']);
                    $logoId = get_term_meta($localCarrier->id, 'thumbnail_id', true);

                    //verify the logo exist
                    if (!get_post($logoId)) {
                        $logoId = null;
                    }

                    if ($logoId && $remoteProductData['logoUrl'] && $remoteProductData['logoUrl'] != get_post_meta($logoId, '_woorefill_logo', true)) {
                        wp_update_attachment_from_url($logoId, $remoteProductData['logoUrl']);
                    }

                } else { //discontinued
                    if (strpos($product->get_name(), $this->discontinuedSuffix) === false) {
                        $product->set_name($product->get_name().' '.$this->discontinuedSuffix);
                    }
                    $product->set_status('pending');
                    $product->save();
                }
            }
        }
    }

    /**
     * @return CarrierManager|object
     */
    public function getCarrierManager()
    {
        return $this->container->get('carrier_manager');
    }

    /**
     * @return ProductManager|object
     */
    public function getProductManager()
    {
        return $this->container->get('product_manager');
    }

    /**
     * @return WooRefillApi
     */
    protected function getRefillAPI()
    {
        return $this->get('refill_api');
    }
}