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

namespace WooRefill\App\Api;

use WooRefill\App\EntityManager\ProductManager;
use WooRefill\App\Model\Carrier;
use WooRefill\App\Model\PaginatedCollection;
use WooRefill\App\Model\Product;

class ProductsEndpoint extends AbstractEndpoint
{
    /**
     * @var ProductManager
     */
    protected $productManager;

    /**
     * @inheritDoc
     */
    public function __construct(WooRefillApi $api, ProductManager $manager)
    {
        parent::__construct($api, '/products');
        $this->productManager = $manager;
    }

    /**
     * @param null  $q
     * @param int   $page
     * @param int   $limit
     * @param array $filters
     *
     * @return PaginatedCollection
     */
    public function getList($q = null, $page = 1, $limit = 30, $filters = [])
    {
        $collection = parent::getList($q, $page, $limit, $filters);

        $skus = [];
        foreach ($collection->items as $product) {
            $skus[] = $product->id;
        }
        $localCarriers = $this->productManager->findAllBySku($skus);

        foreach ($collection->items as $product) {
            foreach ($localCarriers as $localProduct) {
                if ($product->id == $localProduct->sku) {
                    $product->localProduct = $localProduct;
                }
            }
        }

        return $collection;
    }

    /**
     * @param $sku
     *
     * @return Product
     */
    public function get($sku)
    {
        /** @var Product $product */
        $product = parent::get($sku);

        $localProduct = $this->productManager->findOneBySku($sku);
        if ($localProduct) {
            $product->localProduct = $localProduct;
        }

        return $product;
    }

    protected function getModeClass()
    {
        return 'WooRefill\App\Model\Product';
    }
}