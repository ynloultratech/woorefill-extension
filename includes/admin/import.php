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
 * Add import action to product menu
 */
add_action(
    'admin_menu',
    function () {
        add_submenu_page(
            'edit.php?post_type=product',
            __('Import Wireless Products'),
            __('Import Wireless Products'),
            'manage_woocommerce',
            'import_wireless_products',
            function () {
                if ($_POST) {
                    try {
                        $importer = new WR_Product_Importer();
                        $products = array_key_value($_POST, 'products');
                        if ( ! $products) {
                            throw new LogicException('Select at least one product to import');
                        }
                        $importer->setProducts($products);
                        $importer->setCarrierId( array_key_value($_POST, 'carrier_id'));
                        $importer->setStatus( array_key_value($_POST, 'status'));

                        if (array_key_value($_POST, 'create_carrier')) {
                            $importer->setCarrierName(array_key_value($_POST, 'carrier_name'));
                        } else {
                            $importer->setCarrier(get_term(array_key_value($_POST, 'append_to_carrier')));
                        }

                        if (array_key_value($_POST, 'create_category')) {
                            $importer->setCategoryName(array_key_value($_POST, 'category_name'));
                        }else{
                            $importer->setCategory(get_term(array_key_value($_POST, 'parent_category')));
                        }

                        $importer->import();

                    } catch (\Exception $e) {
                        $error = $e->getMessage();
                    }
                }

                include __DIR__.DIRECTORY_SEPARATOR.'templates/import.php';
            }
        );
    }
);

add_action(
    'wp_ajax_get_wireless_carriers',
    function () {
        wr_render_ajax_template(__DIR__.DIRECTORY_SEPARATOR.'templates/ajax_carriers.php');
    }
);

add_action(
    'wp_ajax_get_wireless_products',
    function () {
        wr_render_ajax_template(__DIR__.DIRECTORY_SEPARATOR.'templates/ajax_products.php');
    }
);
