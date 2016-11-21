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

class WooRefillAPI
{
    const POST = 'POST';
    const GET = 'GET';

    /**
     * @var string
     */
    private static $baseUrl = 'http://woorefill-api.dev/api/v1.0';

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
     * @return array
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

        $json = self::send(self::POST, sprintf('/product/%s/submit', $id), $data);

        if ($json && array_key_value($json, 'status') === 'COMPLETED') {
            return $json;
        }
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
        self::$errorCode = null;
        self::$errorMessage = null;

        $baseUrl = self::$baseUrl;
        $apiKey = get_woo_refill_api_key();
        $url = $baseUrl."$url?apikey={$apiKey}";

        woorefill_log(sprintf('Connecting to API url: %s ', str_replace($apiKey, '{hidden}', $url)));

        if ($method === self::POST) {
            $response = wp_remote_post($url, $data);
        } else {
            $response = wp_remote_get($url);
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

        if ($json && $error = array_key_value($json, 'error')) {
            self::$errorCode = array_key_value($error, 'code');
            self::$errorMessage = array_key_value($error, 'message');
        }

        if ( ! $json) {
            woorefill_log(sprintf('API Response (Full): %s', print_r($response, true)));
        }

        return $json;
    }
}