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
    /**
     * @var string
     */
    private static $baseUrl = 'http://woorefill-api.dev/api/v1.0';

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
        $baseUrl = self::$baseUrl;
        $apiKey = get_woo_refill_api_key();
        $url = $baseUrl."/product/$id/submit?apikey={$apiKey}";

        woorefill_log("Connecting to API url: ".str_replace($apiKey, '{hidden}', $url));
        woorefill_log("Sending data: ".print_r($data, true));

        $response = wp_remote_post($url, $data);

        if (isset($response['body'])) {
            woorefill_log("API Response (Json): ".$response['body']);
            $json = json_decode($response['body'], true);
        } else {
            $json = null;
        }

        woorefill_log("API Response (Response Data): ".print_r($json, true));

        if (isset($response['response']['code'], $response['response']['message'])) {
            woorefill_log("API Response (Status): ".$response['response']['code'].'-'.$response['response']['message']);
        }


        if ($json && $error = array_key_value($json, 'error')) {
            throw new \Exception(array_key_value($error, 'message'), array_key_value($error, 'code'));
        }

        if ($json && array_key_value($json, 'status') === 'COMPLETED') {
            return $json;
        }

        woorefill_log("API Response (Full): ".print_r($response, true));
        throw new \Exception("Unknown Error, the order cant be processed");
    }
}