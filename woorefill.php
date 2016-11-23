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
Version: 1.0-alpha.4
Author: YnloUltratech
*/

if ( ! defined('ABSPATH')) {
    exit;
}

include_once __DIR__.'/includes/includes.php';

if (is_admin()) {
    new YnloUltratechGitHubPluginUpdater(__FILE__, 'ynloultratech', "woorefill-extension");

    add_action(
        'admin_enqueue_scripts',
        function () {
            wp_enqueue_script('datatables', plugins_url('/public/admin/js/jquery.dataTables.min.js', __FILE__));
            wp_enqueue_script('woorefill_admin_core', plugins_url('/public/admin/js/woorefill_admin.core.js', __FILE__));
            wp_enqueue_script('jquery_validate', plugins_url('/public/admin/js/jquery.validate.min.js', __FILE__));
            wp_enqueue_style('datatables_css', plugins_url('/public/admin/css/jquery.dataTables.min.css', __FILE__));
            wp_enqueue_style('woorefill_admin_core', plugins_url('/public/admin/css/woorefill_admin.core.css', __FILE__));
        }
    );
}

/**
 * @param $var
 */
function dump($var)
{
    $output = var_export($var, true);
    echo <<<HTML
<pre>
    $output
</pre>
HTML;
}