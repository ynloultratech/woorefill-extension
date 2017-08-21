<?php

/*
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 *
 * @author YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package woorefill-extension
 * @version 1.0.x
 */

namespace WooRefill\App\Twig\Extension;

/**
 * Class WPHelperExtension
 */
class WPHelperExtension extends \WooRefill_Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \WooRefill_Twig_SimpleFilter('trans', [$this, 'trans']),
            new \WooRefill_Twig_SimpleFilter('price', [$this, 'price']),
            new \WooRefill_Twig_SimpleFilter('dump', [$this, 'dump']),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getFunctions()
    {
        return [
            new \WooRefill_Twig_SimpleFunction('dump', [$this, 'dump']),
            new \WooRefill_Twig_SimpleFunction('wc_text_input', [$this, 'wcTextInput']),
            new \WooRefill_Twig_SimpleFunction('wc_checkbox', [$this, 'wcCheckbox']),
            new \WooRefill_Twig_SimpleFunction('do_action', [$this, 'doAction']),
            new \WooRefill_Twig_SimpleFunction('call', [$this, 'call']),
            new \WooRefill_Twig_SimpleFunction('call_get', [$this, 'callGet']),
            new \WooRefill_Twig_SimpleFunction('admin_url', [$this, 'adminUrl']),
            new \WooRefill_Twig_SimpleFunction('ajax_admin_url', [$this, 'ajaxAdminUrl']),
            new \WooRefill_Twig_SimpleFunction('paginate_links', [$this, 'paginateLinks']),
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
     * wcCheckbox
     *
     * @param $args
     *
     * @return string
     */
    public function wcCheckbox($args = [])
    {
        ob_start();
        woocommerce_wp_checkbox($args);

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

    public function paginateLinks($page, $pages)
    {
        return paginate_links(
            [
                'base' => add_query_arg('pagenum', '%#%'),
                'prev_text' => __('&laquo;', 'text-domain'),
                'next_text' => __('&raquo;', 'text-domain'),
                'total' => $pages,
                'current' => $page,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'wp_helper_extension';
    }
}