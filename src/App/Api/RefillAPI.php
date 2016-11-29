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

namespace WooRefill\App\Api;

use WooRefill\App\Logger\Logger;
use WooRefill\GuzzleHttp\Client;

/**
 * Class RefillAPI
 */
class RefillAPI
{
    const POST = 'post';
    const GET = 'get';

    /**
     * @var string
     */
    protected $api;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * RefillAPI constructor.
     */
    public function __construct($api, $apiKey, Logger $logger)
    {
        $this->api = $api;
        $this->apiKey = $apiKey;
        $this->logger = $logger;
    }

    /**
     * @return string
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * @param string $api
     *
     * @return $this
     */
    public function setApi($api)
    {
        $this->api = $api;

        return $this;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     *
     * @return $this
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param Logger $logger
     *
     * @return $this
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @return \stdClass
     */
    public function getCarrier($carrierId)
    {
        return $this->send(self::GET, '/carrier/'.$carrierId);
    }

    /**
     * @return array
     */
    public function getCarriers()
    {
        return $this->send(self::GET, '/carrier');
    }

    /**
     * @return array
     */
    public function getProducts($carrierId = null)
    {
        return $this->send(self::GET, '/product'.($carrierId ? "?carrier_id=$carrierId" : ''));
    }

    /**
     * @return array
     */
    public function getProduct($productId)
    {
        return $this->send(self::GET, '/product/'.$productId);
    }

    /**
     * @param integer $wirelessId
     * @param null    $orderId
     * @param array   $meta
     *
     * @return array|false
     * @throws \Exception
     */
    public function submit($wirelessId, $orderId = null, $meta = [])
    {
        $this->logger->addLog('Submitting order #%s', $orderId);
        $data = array_merge(
            [
                'correlation_id' => $orderId,
            ],
            $meta
        );

        $transaction = $this->send(self::POST, sprintf('/product/%s/submit', $wirelessId), $data);

        if ($transaction && $transaction->status === 'COMPLETED') {
            $this->logger->addLog('The order #%s has been completed successfully.', $orderId);

            return $transaction;
        }

        $message = sprintf('Invalid API response processing order #%s', $orderId);
        $this->logger->addErrorLog($message);
        throw new \Exception($message);
    }

    /**
     * send
     *
     * @param       $method
     * @param       $url
     * @param array $data
     *
     * @return array|mixed|null|object
     * @throws \Exception
     */
    protected function send($method, $url, array $data = [])
    {
        $apiKey = $this->apiKey;
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
        $url = $this->api.$url;

        //woorefill_log(sprintf('Connecting to API url: %s ', $url));
        try {
            $client = new Client();
            $options = [
                'form_params' => $data,
                'headers' => [
                    'APIKey' => $apiKey,
                ],
            ];
            $response = $client->request($method, $url, $options);
            $json = $response->getBody()->getContents();
            $result = @json_decode($json);

            if ($result) {
                if ($result && $error = $result->error) {
                    $error = $result->error->code;
                    $message = $result->error->message;
                    throw new \Exception((string)$message, (int)$error);
                }

                set_transient($cacheKey, $result, 120);

                return $result;

            } else {
                throw new \Exception('Invalid API response');
            }
        } catch (\Exception $e) {
            //log error
            throw  $e;
        }
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