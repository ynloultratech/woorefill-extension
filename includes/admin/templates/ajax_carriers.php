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

$carriers = WooRefillAPI::getCarriers();

?>
<?php if ($carriers): ?>
    <div id="wireless-carriers-table-wrapper" style="visibility: hidden">
        <table id="wireless-carriers-table" class="wp-list-table widefat fixed striped tags" style="width:100%;">
            <thead>
            <tr>
                <th scope="col" style="width: 30px"><?= __('ID'); ?></th>
                <th scope="col"><?= __('Name'); ?></th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($carriers as $carrier): ?>
                <tr>
                    <td><?= $carrier['id'] ?></td>
                    <td><strong id="carrier-name-<?= $carrier['id'] ?>"><?= $carrier['name'] ?></strong></td>
                    <td style="text-align: right">
                        <a id="carrier-select-<?= $carrier['id'] ?>" href="javascript: showProducts('<?= $carrier['id'] ?>');" class="button <?= array_key_value($_POST, 'carrier_id') == $carrier['id'] ? 'button-primary active' : '' ?>">
                            <?php _e('Import'); ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script>
        jQuery(document).ready(function () {
            var table = jQuery('#wireless-carriers-table');
            table.DataTable({
                order: [[2, 'asc']]
            });
            //avoid a flicker when the table is loading
            jQuery('#wireless-carriers-table-wrapper').css('visibility', 'visible')
        });
        function showProducts($carrierId) {
            jQuery('#wireless-carriers-table').find('a.button').removeClass('button-primary active');
            jQuery('#carrier-select-' + $carrierId).addClass('button-primary active');
            jQuery('#col-right').show();
            jQuery('#wireless-products-loader').show();
            var productsWrapper = jQuery('#wireless-products-wrapper');
            productsWrapper.hide();
            productsWrapper.load('/wp-admin/admin-ajax.php?action=get_wireless_products&carrier_id=' + $carrierId, function () {
                jQuery('#wireless-products-loader').hide();
                var carrierName = jQuery('#carrier-name-' + $carrierId).html();
                jQuery('#carrier_name').val(carrierName);
                jQuery('.carrier-name').html(carrierName);
                productsWrapper.show(500);
            });
        }
    </script>
<?php endif; ?>