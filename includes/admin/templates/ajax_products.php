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

$carrierId = array_key_value($_POST, 'carrier_id');
if ( ! $carrierId) {
    $carrierId = array_key_value($_GET, 'carrier_id');
}
$products = WooRefillAPI::getProducts($carrierId);

?>
<?php if ($products): ?>
    <div class="form-wrap">
        <form id="import-products-form" action="/wp-admin/edit.php?post_type=product&page=import_wireless_products" method="post">
            <input hidden name="carrier_id" value="<?= $carrierId ?>">
            <h3 style="margin-bottom: 0">Products to import from "<span class="carrier-name"><?= array_key_value($_POST, 'carrier_name') ?></span>"</h3>
            <p><?= __('Check the list of products to import from this carrier.'); ?></p>

            <div id="wireless-products-table-wrapper">
                <table id="wireless-products-table" class="wp-list-table widefat fixed striped tags" style="width:100%">
                    <thead>
                    <tr>
                        <td id="cb" class="manage-column column-cb check-column">
                            <?php
                            $allProducts = ($_POST && array_key_value($_POST, 'products_all')) || ! $_POST;
                            ?>
                            <input name="products_all" value="1" id="cb-select-all-1" onchange="selectAllProductsToggle()" type="checkbox" <?= $allProducts ? 'checked' : '' ?> >
                        </td>
                        <th scope="col"><?= __('ID'); ?></th>
                        <th scope="col"><?= __('Name'); ?></th>
                        <th scope="col"><?= __('Type'); ?></th>
                        <th scope="col"><?= __('Discount Rate'); ?></th>
                        <th scope="col"><?= __('Price'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <th scope="row" class="check-column">
                                <?php
                                $selected = (array)array_key_value($_POST, 'products', $_POST ? [] : [$product['id']]);
                                if (in_array($product['id'], $selected)) {
                                    $checked = 'checked';
                                } else {
                                    $checked = null;
                                }
                                ?>
                                <input type="checkbox" name="products[]" value="<?= $product['id'] ?>" id="product_<?= $product['id'] ?>" <?= $checked ?>>
                            </th>
                            <td><?= $product['id'] ?></td>
                            <td><strong><?= $product['name'] ?></strong></td>
                            <td><strong><?= $product['type'] ?></strong></td>
                            <td><strong><?= number_format($product['discount_rate'], 2) ?> %</strong></td>
                            <td>
                                <strong>
                                    <?php
                                    if ($product['variable_amount']) {
                                        echo wc_price($product['min_amount']).' - '.wc_price($product['max_amount']);
                                    } else {
                                        echo wc_price($product['amount']);
                                    }
                                    ?>
                                </strong>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <hr>
            <div class="form-field">
                <label style="display: inline-block">
                    <input id="create_carrier_1" name="create_carrier" type="radio" value="1" <?= (array_key_value($_POST, 'create_carrier', 1) == 1) ? 'checked' : '' ?>/>
                    <?= __('Create new carrier'); ?>
                </label>
                <label style="display: inline-block; margin-left: 20px">
                    <input id="create_carrier_0" name="create_carrier" type="radio" value="0" <?= array_key_value($_POST, 'create_carrier', 1) == 0 ? 'checked' : '' ?>/>
                    <?= __('Use existent carrier'); ?>
                </label>
                <p><?= __('Import selected products into existent carrier or create new one?'); ?></p>
            </div>

            <div class="form-field select-existent-carrier">
                <label for="tag-name"> <?= __('Carrier'); ?></label>
                <?php
                wp_dropdown_categories(
                    [
                        'name' => 'append_to_carrier',
                        'show_option_none' => __('Select a Carrier'),
                        'option_none_value' => null,
                        'taxonomy' => 'product_cat',
                        'selected' => array_key_value($_POST, 'append_to_carrier', 0),
                        'hide_empty' => false,
                        'show_count' => true,
                        'required' => true,
                        'meta_key' => 'wireless_carrier',
                        'meta_value' => '1',
                    ]
                ) ?>
                <p><?= __('Select existent carrier in order to import selected products.'); ?></p>
            </div>

            <div class="create-new-carrier">
                <div class="form-field">
                    <label for="carrier_name"><?= __('Carrier') ?></label>
                    <input id="carrier_name" name="carrier_name" value="<?= array_key_value($_POST, 'carrier_name') ?>" type="text" required/>
                </div>

                <div class="form-field">

                    <div class="form-field">
                        <label style="display: inline-block">
                            <input id="create_category_1" name="create_category" type="radio" value="1" <?= array_key_value($_POST, 'create_category', 0) == 1 ? 'checked' : '' ?>/>
                            <?= __('Create new category'); ?>
                        </label>
                        <label style="display: inline-block; margin-left: 20px">
                            <input id="create_category_0" name="create_category" type="radio" value="0" <?= array_key_value($_POST, 'create_category', 0) == 0 ? 'checked' : '' ?>/>
                            <?= __('Use existent category'); ?>
                        </label>
                        <p><?= __('Create the carrier into existent category or create new one?'); ?></p>
                    </div>

                    <div class="select-existent-category">
                        <label for="tag-name"> <?= __('Category'); ?></label>
                        <?php
                        wp_dropdown_categories(
                            [
                                'taxonomy' => 'product_cat',
                                'name' => 'parent_category',
                                'hide_empty' => false,
                                'selected' => array_key_value($_POST, 'parent_category', 0),
                                'required' => true,
                                'meta_query' => [
                                    [
                                        'key' => 'wireless_carrier',
                                        'compare' => 'NOT EXISTS',
                                        'value' => '',
                                    ],
                                ],
                            ]
                        ) ?>
                        <p><?= __('Select the category to use for this carrier.'); ?></p>
                    </div>

                    <div class="form-field create-new-category">
                        <label for="category_name"><?= __('Category') ?></label>
                        <input id="category_name" name="category_name" required value="<?= array_key_value($_POST, 'category_name') ?>" type="text"/>
                        <p><?= __('Create new category for this carrier, e.g. Refills or Long Distance'); ?></p>
                    </div>


                </div>
            </div>
            <div class="form-field">
                <label for="status"> <?= __('Product status'); ?></label>
                <select id="status" name="status">
                    <?php
                    $statuses = ['pending', 'draft', 'publish'];
                    foreach ($statuses as $status) {
                        $selected = array_key_value($_POST, 'status', 'publish') == $status ? 'selected' : '';
                        echo "<option value='$status' $selected >".ucfirst($status).'</option>';
                    }
                    ?>
                </select>
                <p><?= __('Status to set for each product after import'); ?></p>
            </div>
            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button button-primary" value="Import">
            </p>
        </form>
        <script>
            jQuery('#import-products-form').validate({
                errorClass: 'form-invalid',
                highlight: function (element, errorClass) {
                    jQuery(element).closest('.form-field').addClass(errorClass);
                }
            });

            jQuery('[name="create_carrier"]').change(function () {
                if (jQuery(this).val() == 1 && jQuery(this).prop('checked')) {
                    jQuery('.create-new-carrier').show();
                    jQuery('.select-existent-carrier').hide();
                } else if (jQuery(this).val() == 0 && jQuery(this).prop('checked')) {
                    jQuery('.create-new-carrier').hide();
                    jQuery('.select-existent-carrier').show();
                }
            }).trigger('change');

            jQuery('[name="create_category"]').change(function () {
                if (jQuery(this).val() == 1 && jQuery(this).prop('checked')) {
                    jQuery('.create-new-category').show();
                    jQuery('.select-existent-category').hide();
                } else if (jQuery(this).val() == 0 && jQuery(this).prop('checked')) {
                    jQuery('.create-new-category').hide();
                    jQuery('.select-existent-category').show();
                }
            }).trigger('change');
        </script>
    </div>
<?php endif; ?>
