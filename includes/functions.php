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

if ( ! defined('ABSPATH')) {
    exit;
}

/**
 * Logging method.
 *
 * @param string $message
 */
function woorefill_log($message)
{
    if (woo_refill_log_enabled()) {
        $log = new WC_Logger();
        if ( ! is_array($message) || is_object($message)) {
            $message = print_r($message, true);
        }
        $log->add('woorefill', $message);
    }
}

function wr_humanize_str($label)
{
    return ucwords(str_replace('_', ' ', $label));
}

if ( ! function_exists('array_key_value')) {
    /**
     * Extract some value from array using a key path,
     * return default value if the path don't exist.
     *
     * Helpful to check a expected value of array in conditions
     *
     * e.g.
     * if (array_key_value($array,'options.enabled') === true){
     *   //...
     * }
     *
     * @param      $array
     * @param      $path
     * @param null $default
     *
     * @return mixed|null
     */
    function array_key_value($array, $path, $default = null)
    {
        $pathArray = explode('.', $path);
        foreach ($pathArray as $index) {
            if (array_key_exists($index, $array)) {
                $value = $array[$index];
                if (is_array($value)) {
                    $array = $value;
                }
            } else {
                return $default;
            }
        }
        if (isset($value)) {
            return $value;
        }

        return $default;
    }
}