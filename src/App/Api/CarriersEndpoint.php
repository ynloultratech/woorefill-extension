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

use WooRefill\App\EntityManager\CarrierManager;
use WooRefill\App\Model\Carrier;
use WooRefill\App\Model\PaginatedCollection;

class CarriersEndpoint extends AbstractEndpoint
{
    /**
     * @var CarrierManager
     */
    protected $carrierManager;

    /**
     * @inheritDoc
     */
    public function __construct(WooRefillApi $api, CarrierManager $manager)
    {
        parent::__construct($api, '/carriers');
        $this->carrierManager = $manager;
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
        foreach ($collection->items as $carrier) {
            $skus[] = $carrier->id;
        }
        $localCarriers = $this->carrierManager->findAllBySku($skus);
        /** @var Carrier $carrier */
        foreach ($collection->items as $carrier) {
            foreach ($localCarriers as $localCarrier) {
                if ($carrier->id == $localCarrier->sku) {
                    $carrier->localCarrier = $localCarrier;
                }
            }
        }

        return $collection;
    }

    /**
     * @param $sku
     *
     * @return Carrier
     */
    public function get($sku)
    {
        $carrier = parent::get($sku);

        $localCarrier = $this->carrierManager->findOneBySku($sku);
        if ($localCarrier) {
            $carrier->localCarrier = $localCarrier;
        }

        return $carrier;
    }

    protected function getModeClass()
    {
        return 'WooRefill\App\Model\Carrier';
    }
}