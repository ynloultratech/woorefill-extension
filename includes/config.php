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

const WR_INPUT_META_PREFIX = '_woo_refill_meta_';
const WR_RESPONSE_PREFIX = '_woo_api_response_';

function allow_buy_multiple_wireless_products()
{
    //TODO: move to some settings in the backend
    return false;
}

function get_woo_refill_api_key()
{
    return get_option('_woorefill_api_key');
}

function woo_refill_log_enabled()
{
    return (get_option('_woorefill_log') === 'yes');
}