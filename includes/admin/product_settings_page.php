<?php

/*
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 *
 * @author YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package woorefill-extension
 * @version 1.0-alpha
 */

if ( ! defined('ABSPATH')) {
    exit;
}

/**
 * Add new type of product to select
 */
add_filter(
    'product_type_selector',
    function ($types) {
        $types[WC_Product_Wireless::PRODUCT_TYPE_WIRELESS] = __('Wireless Plan');

        return $types;
    }
);

/**
 * Customize product type options
 */
add_filter(
    'product_type_options',
    function ($options) {
        $options['virtual']['wrapper_class'] .= ' show_if_wireless';
        $options['virtual']['downloadable'] .= ' hide_if_wireless';

        return $options;
    }
);

/**
 * Customize product page tabs
 */
add_filter(
    'woocommerce_product_data_tabs',
    function ($tabs) {
        $tabs['shipping']['class'][] = 'hide_if_wireless';

        return $tabs;
    }
);

add_action(
    'save_post',
    function ($post_id) {
        if (isset($_POST['_wireless_product_id']) && $_POST['_wireless_product_id']) {
            update_post_meta($post_id, '_wireless_product_id', $_POST['_wireless_product_id']);
        }
    }
);
/**
 * Force show the pricing group for this new product type
 */
add_action(
    'woocommerce_product_options_pricing',
    function () {
        $productIdField = woocommerce_wp_text_input(
            [
                'id'            => '_wireless_product_id',
                'label'         => __('Wireless Product ID'),
                'desc_tip'      => 'true',
                'wrapper_class' => 'show_if_wireless',
                'description'   => __('This product ID is used to communicate with the refill API and make the refill.'),
            ]
        );
        echo <<<HTML
<script>
    jQuery('.options_group.pricing').addClass('show_if_wireless');
    var forceVirtual = function(){
        if (jQuery('#product-type').val() === 'wireless'){
            jQuery('#_virtual').prop('checked', true)
            jQuery('._sale_price_field').hide();
            jQuery('#_sale_price').val('');
            jQuery('#_sku').val('');
            jQuery('#_sold_individually').prop('checked', true)
        }else{
            jQuery('._sale_price_field').show();
        }
    };
    jQuery('#_virtual').on('change', forceVirtual);
    jQuery('#product-type').on('change', forceVirtual);
</script>
    $productIdField
HTML;
    }
);

/**
 * Quick edit fields
 */
add_action(
    'woocommerce_product_quick_edit_end',
    function () {
        echo <<<HTML
<script>
    //TODO: show or hide fields according to product type
   // jQuery('[name="_sku"]').closest('label').hide().next('.clear').hide();
   // jQuery('[name="_shipping_class"]').closest('label').hide().next('.clear').hide();
   // jQuery('.price_fields').show();
</script>
HTML;
    }
);
