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

namespace WooRefill\App\Api;

use WooRefill\App\Logger\Logger;
use WooRefillJMS\Serializer\Serializer;
use WooRefillJMS\Serializer\SerializerBuilder;
use WooRefillSymfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class WooRefillApi
 */
class WooRefillApi
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
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var TransactionsEndpoint
     */
    protected $transactions;

    /**
     * @var CarriersEndpoint
     */
    protected $carriers;

    /**
     * @var ProductsEndpoint
     */
    protected $products;

    /**
     * WooRefillApi constructor.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->api = $container->getParameter('api_url');
        $this->apiKey = $container->getParameter('api_key');
        $this->logger = $container->get('logger');

        $this->serializer = SerializerBuilder::create()->build();

        $this->carriers = new CarriersEndpoint($this, $container->get('carrier_manager'));
        $this->products = new ProductsEndpoint($this, $container->get('product_manager'));
        $this->transactions = new TransactionsEndpoint($this);
    }

    /**
     * @return Serializer
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * @return CarriersEndpoint
     */
    public function getCarriers()
    {
        return $this->carriers;
    }

    /**
     * @return ProductsEndpoint
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @return TransactionsEndpoint
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /**
     * @param string $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * send
     *
     * @param                     $method
     * @param                     $url
     * @param array               $params
     * @param string|array|object $body
     *
     * @return array|mixed|null|object
     * @throws \Exception
     */
    public function request($method, $url, array $params = [], $body = null)
    {
        $apiKey = $this->apiKey;

        $url = $this->api.$url;
        if ($params) {
            $query = build_query($params);
            $url .= '?'.$query;
        }

        try {
            $options = [
                'timeout' => 60,
                'body' => $body,
                'headers' => [
                    'APIKey' => $apiKey,
                ],
            ];

            if (self::GET === $method) {
                $response = wp_remote_get($url, $options);
            } else {
                $response = wp_remote_post($url, $options);
            }

            if ($response instanceof \WP_Error) {
                throw new \Exception($response->get_error_message());
            }

            $result = @json_decode($response['body'], true);
            if ($result !== null) {
                if ($result && @$response['response']['code'] >= 400 && @$result['code']) {
                    $error = $result['code'];
                    $message = $result['message'];

                    throw new \Exception((string) $message, (int) $error);
                }

                return $result;

            }

            throw new \Exception('Invalid API response');

        } catch (\Exception $e) {
            //log error
            throw  $e;
        }
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }
}