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

namespace WooRefill\App\Twig\Extension;

/**
 * Class WPHelperExtension
 */
class WPHelperExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('trans', [$this, 'trans']),
            new \Twig_SimpleFilter('price', [$this, 'price']),
            new \Twig_SimpleFilter('dump', [$this, 'dump']),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('wc_text_input', [$this, 'wcTextInput']),
            new \Twig_SimpleFunction('do_action', [$this, 'doAction']),
            new \Twig_SimpleFunction('call', [$this, 'call']),
            new \Twig_SimpleFunction('call_get', [$this, 'callGet']),
            new \Twig_SimpleFunction('admin_url', [$this, 'adminUrl']),
            new \Twig_SimpleFunction('ajax_admin_url', [$this, 'ajaxAdminUrl']),
        ];
    }

    /**
     * @param string $path
     * @param string $scheme
     *
     * @return string
     */
    public function adminUrl($path = '', $scheme = 'admin')
    {
        return admin_url($path, $scheme);
    }

    /**
     * @param string $query
     * @param string $scheme
     *
     * @return string
     */
    public function ajaxAdminUrl($query = '', $scheme = 'admin')
    {
        return admin_url('admin-ajax.php'.$query, $scheme);
    }


    /**
     * trans
     *
     * @param string $message
     * @param array  $params
     * @param null   $domain
     *
     * @return string|void
     */
    public function trans($message, $params = [], $domain = null)
    {
        return _x($message, $params, $domain);
    }

    /**
     * trans
     *
     * @param       $price
     * @param array $args
     *`
     *
     * @return string
     */
    public function price($price, $args = [])
    {
        return wc_price($price, $args);
    }

    /**
     * Call any function and pass some arguments
     *
     * @param string $function
     *
     * @return mixed
     */
    public function call($function)
    {
        return call_user_func_array($function, array_slice(func_get_args(), 1));
    }

    /**
     * Call any function pass arguments and get the output
     *
     * @param string $function
     *
     * @return mixed
     */
    public function callGet($function)
    {
        ob_start();
        call_user_func_array($function, array_slice(func_get_args(), 1));

        return ob_get_clean();
    }

    /**
     * doAction
     *
     * @param       $tag
     * @param array $args
     */
    public function doAction($tag, $args = [])
    {
        do_action($tag, $args);
    }

    /**
     * wcTextInput
     *
     * @param $args
     *
     * @return string
     */
    public function wcTextInput($args = [])
    {
        ob_start();
        woocommerce_wp_text_input($args);

        return ob_get_clean();
    }

    /**
     * dum
     *
     * @param $var
     */
    public function dump($var)
    {
        if (function_exists('dump')) {
            return dump($var);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'wp_helper_extension';
    }
}