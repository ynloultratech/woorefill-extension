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

namespace WooRefill\Admin\Page;

use WooRefill\App\DependencyInjection\CommonServiceTrait;
use WooRefill\Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Class Order
 */
class Order implements ContainerAwareInterface
{
    use CommonServiceTrait;

    /**
     */
    public function orderDataAfterDetails()
    {
        $metaArray = get_post_meta(get_the_ID(), null, true);
        $details = [];
        foreach ($metaArray as $meta => $value) {
            if (strpos($meta, '_woo_refill_meta_') !== false) {
                $name = str_replace('_woo_refill_meta_', '', $meta);
                $value = current($value);
                if ($value) {
                    $details[$name]= $value;
                }
            }
            if (strpos($meta, '_woo_api_response_') !== false) {
                $name = str_replace('_woo_api_response_', '', $meta);
                $value = current($value);
                if ($value) {
                    $details[$name]= $value;
                }
            }
        }

        $this->render('@Admin/order/refill_details.html.twig', ['details' => $details]);
    }
}