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

namespace WooRefill\Admin\Page;

use WooRefill\App\DependencyInjection\CommonServiceTrait;
use WooRefill\Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Class ProductSettings
 */
class ProductSettings implements ContainerAwareInterface
{
    use CommonServiceTrait;

    /**
     * productTypeSelector
     *
     * @param array $types
     *
     * @return mixed
     */
    public function productTypeSelector($types)
    {
        $types[\WC_Product_Wireless::PRODUCT_TYPE_WIRELESS] = __('Wireless Plan');

        return $types;
    }

    /**
     * productTypeOptions
     *
     * @param $options
     *
     * @return mixed
     */
    public function productTypeOptions($options)
    {
        $options['virtual']['wrapper_class'] .= ' show_if_wireless';
        $options['virtual']['downloadable'] .= ' hide_if_wireless';

        return $options;
    }

    /**
     * productDataTabs
     *
     * @param $tabs
     *
     * @return mixed
     */
    public function productDataTabs($tabs)
    {
        $tabs['shipping']['class'][] = 'hide_if_wireless';

        return $tabs;
    }

    /**
     * productOptionsPricing
     */
    public function productOptionsPricing()
    {
       $this->render('@Admin/product_settings/product_pricing.html.twig');
    }

    /**
     * saveProduct
     *
     * @param $post_id
     */
    public function saveProduct($post_id)
    {
        if (isset($_POST['_wireless_product_id']) && $_POST['_wireless_product_id']) {
            update_post_meta($post_id, '_wireless_product_id', $_POST['_wireless_product_id']);
        }
    }
}