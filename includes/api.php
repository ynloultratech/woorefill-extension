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

class WooRefillAPI
{
    const POST = 'POST';
    const GET = 'GET';

    /**
     * @var string
     */
    private static $errorCode = null;

    /**
     * @var string
     */
    private static $errorMessage = null;

    /**
     * @param       $id
     * @param null  $orderId
     * @param array $meta
     *
     * @return array|false
     * @throws Exception
     */
    public static function submit($id, $orderId = null, $meta = [])
    {
        $data = array_merge(
            [
                'correlation_id' => $orderId,
            ],
            $meta
        );

        $transaction = self::send(self::POST, sprintf('/product/%s/submit', $id), $data);

        if ($transaction && array_key_value($transaction, 'status') === 'COMPLETED') {
            return $transaction;
        }

        return false;
    }

    /**
     * @param $id
     *
     * @return array|mixed|null|object
     */
    public static function getProduct($id)
    {
        return self::send(self::GET, sprintf('/product/%s', $id));
    }

    /**
     * @return array
     */
    public static function getCarriers()
    {
        return self::send(self::GET, '/carrier');
    }

    /**
     * @param string|integer $carrierId
     *
     * @return array
     */
    public static function getProducts($carrierId = null)
    {
        return self::send(self::GET, '/product'.($carrierId ? '?carrier_id='.$carrierId : ''));
    }

    /**
     * send
     *
     * @param       $method
     * @param       $url
     * @param array $data
     *
     * @return array|mixed|null|object
     * @throws Exception
     */
    protected static function send($method, $url, array $data = [])
    {
        $apiKey = get_woo_refill_api_key();
        $cacheKey = self::makeCacheKey(
            [
                'method' => $method,
                'url' => $url,
                'data' => $data,
                'apikey' => $apiKey,
            ]
        );

        if ($method === self::GET && $cache = get_transient($cacheKey)) {
            return $cache;
        }

        self::$errorCode = null;
        self::$errorMessage = null;

        $url = WR_API_BASE_URL.$url;
        woorefill_log(sprintf('Connecting to API url: %s ', $url));

        if ($method === self::POST) {
            woorefill_log(sprintf('Post data: %s', print_r($data, true)));
            $response = wp_remote_post($url, ['body' => $data, 'headers' => ['APIKey' => $apiKey]]);
        } else {
            $response = wp_remote_get($url, ['headers' => ['APIKey' => $apiKey]]);
        }

        if ($response instanceof WP_Error) {
            self::$errorCode = $response->get_error_code();
            self::$errorMessage = $response->get_error_message();
            woorefill_log(sprintf('Error (%s): %s', self::$errorCode, self::$errorMessage));
            throw new Exception(self::$errorMessage);
        }

        if (isset($response['response']['code'], $response['response']['message'])) {
            woorefill_log(sprintf('API Response (Status): %s-%s', $response['response']['code'], $response['response']['message']));
        }

        if (array_key_exists('body', $response)) {
            woorefill_log(sprintf('API Response (Json): %s', $response['body']));
            $json = json_decode($response['body'], true);
        } else {
            $json = null;
        }

        if ( ! $json) {
            woorefill_log(sprintf('API Response (Full): %s', print_r($response, true)));
        }

        if ($json && $error = array_key_value($json, 'error')) {
            self::$errorCode = array_key_value($error, 'code');
            self::$errorMessage = array_key_value($error, 'message');
            woorefill_log(sprintf('API Error (%s): %s', self::$errorCode, self::$errorMessage));
            throw new Exception((string)self::$errorMessage, (int)self::$errorCode);
        }

        set_transient($cacheKey, $json, 120);

        return $json;
    }

    /**
     * makeCacheKey
     *
     * @param array $data
     *
     * @return string
     */
    private static function makeCacheKey($data)
    {
        return 'woo_refill_api_'.md5(serialize($data));
    }
}