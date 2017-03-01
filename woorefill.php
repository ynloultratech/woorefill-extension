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
Version: 1.0.8
Author: YnloUltratech
*/

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WooCommerce')) {
    return;
}

use WooRefill\App\Kernel;

include_once __DIR__.'/src/includes/wc-product-wireless.php';
include __DIR__.'/vendor/autoload.php';

if (file_exists(__DIR__.'/dev/dev.php')) {
    include __DIR__.'/dev/dev.php';
}

if (!defined('WOOREFILL_DEBUG')) {
    define('WOOREFILL_DEBUG', false);
}

Kernel::init();