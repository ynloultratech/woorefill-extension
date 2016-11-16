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

global $product;

if ( ! $product->is_purchasable()) {
    return;
}

?>

<?php
// Availability
$availability      = $product->get_availability();
$availability_html = empty($availability['availability']) ? '' : '<p class="stock '.esc_attr($availability['class']).'">'.esc_html($availability['availability']).'</p>';

echo apply_filters('woocommerce_stock_html', $availability_html, $availability['availability'], $product);
?>

<?php if ($product->is_in_stock() && ! wc_product_in_cart($product)) : ?>

    <?php do_action('woocommerce_before_add_to_cart_form'); ?>

    <form action="<?= wc_get_checkout_url() ?>" class="cart" method="post" enctype='multipart/form-data'>
        <?php do_action('woocommerce_before_add_to_cart_button'); ?>

        <input type="hidden" name="add-to-cart" value="<?php echo esc_attr($product->id); ?>"/>

        <button type="submit" class="single_add_to_cart_button button alt"><?php echo esc_html($product->add_to_cart_text()); ?></button>

        <?php do_action('woocommerce_after_add_to_cart_button'); ?>
    </form>

    <?php do_action('woocommerce_after_add_to_cart_form'); ?>

<?php endif; ?>