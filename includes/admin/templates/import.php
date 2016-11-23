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

?>

<div class="wrap woocommerce">
    <div class="icon32 icon32-attributes" id="icon-woocommerce"><br/></div>
    <h1><?= __('Import Wireless Products'); ?></h1>

    <?php if (isset($error)): ?>
        <div class="notice notice-error"><p><?= $error ?></p></div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <div class="notice notice-success"><p><?= $success ?></p></div>
    <?php endif; ?>

    <br class="clear"/>
    <?php $submittedImport = (boolean)array_key_value($_POST, 'carrier_id'); ?>
    <div id="col-container">
        <div id="col-left">
            <div class="col-wrap">
                <div id="wireless-carriers-wrapper">
                    <?php if ($submittedImport): ?>
                        <?php include __DIR__.DIRECTORY_SEPARATOR.'ajax_carriers.php'; ?>
                    <?php else: ?>
                        <div style="text-align: center; padding: 50px">
                            <img src="/wp-admin/images/spinner-2x.gif">
                        </div>
                    <?php endif; ?>
                </div>
                <?php if ( ! $submittedImport): ?>
                    <script>
                        jQuery('#wireless-carriers-wrapper').load('/wp-admin/admin-ajax.php?action=get_wireless_carriers')
                    </script>
                <?php endif; ?>
            </div>
        </div>
        <div id="col-right" style="<?php if ( ! $submittedImport): ?>display: none;<?php endif; ?>">
            <div class="col-wrap">
                <div id="wireless-products-wrapper" style="<?php if ( ! $submittedImport): ?>display: none<?php endif; ?>">
                    <?php if ($submittedImport): ?>
                        <?php include __DIR__.DIRECTORY_SEPARATOR.'ajax_products.php'; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div id="wireless-products-loader" style="text-align: center; padding: 50px; <?php if ($submittedImport): ?>display: none<?php endif; ?>">
                <img src="/wp-admin/images/spinner-2x.gif">
            </div>
        </div>
    </div>
</div>
