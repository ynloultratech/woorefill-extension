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

/*
Plugin Name: WooRefill
Plugin URI: https://github.com/ynloultratech/woorefill-extension
Description: WooRefill is a extension for WooCommerce to add wireless plans to your shop and do refills.
Version: 1.0.26
Author: YnloUltratech
*/

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WooCommerce')) {
    add_action(
        'admin_notices',
        function () {
            $notice = <<<HTML
    <div class="notice notice-error is-dismissible">
        <p>WooRefill extension is <strong>Enabled</strong> but require 
        <a href="https://wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce</a> to works. Please install <a href="https://wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce</a> plugin to add products and accept payments.
    </div>
HTML;
            echo $notice;

        }
    );

    return;
}

if (!version_compare(WooCommerce::instance()->version, '3.0', '>=')) {
    add_action(
        'admin_notices',
        function () {
            $version = WooCommerce::instance()->version;
            $notice = <<<HTML
    <div class="notice notice-error is-dismissible">
        <p>WooRefill require <a href="https://wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce</a> version 3.0 or greater. 
        Your current version ($version) is not compatible.</p>
    </div>
HTML;
            echo $notice;

        }
    );

    return;
}

use WooRefill\App\Kernel;

include_once __DIR__.'/src/includes/wc-product-wireless.php';
include __DIR__.'/autoload.php';
include __DIR__.'/functions.php';

if (file_exists(__DIR__.'/dev/dev.php')) {
    include __DIR__.'/dev/dev.php';
}

if (!defined('WOOREFILL_DEBUG')) {
    define('WOOREFILL_DEBUG', false);
}

add_action(
    'init',
    function () {
        if (get_transient('_woorefill_products_synchronized') === false) {
            $process = new \WooRefill\App\Sync\ProductSyncRequest();
            $process->dispatch();
        }
    }
);

Kernel::init();