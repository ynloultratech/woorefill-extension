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

/*
Plugin Name: WooRefill
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: WooRefill is a extension for WooCommerce to add wireless plans to your shop and do refills.
Version: 1.0-alpha
Author: YnloUltratech
*/

if ( ! defined('ABSPATH')) {
    exit;
}

include_once __DIR__.'/includes/includes.php';

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