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

    //
    //    /**
    //     * @return \stdClass
    //     */
    //    public function getCarrier($carrierId)
    //    {
    //        $carriers = $this->send(self::GET, '/carriers/'.$carrierId);
    //    }
    //
    //    /**
    //     * @return array
    //     */
    //    public function getCarriers()
    //    {
    //        $carriers = $this->send(self::GET, '/carriers');
    //        $carriers = $this->serializer->fromArray($carriers['items'], 'array<WooRefill\App\Model\Carrier>');
    //        dump($carriers);
    //        exit;
    //    }
    //
    //    /**
    //     * @return array
    //     */
    //    public function getProducts($carrierId = null)
    //    {
    //        $products = $this->send(self::GET, '/products'.($carrierId ? "?carrier_id=$carrierId" : ''));
    //
    //        //   dump($products);exit;
    //        return $products;
    //    }
    //
    //    /**
    //     * @return array
    //     */
    //    public function getProduct($productId)
    //    {
    //        $products = $this->send(self::GET, '/products/'.$productId);
    //        //print_r($products);
    //        //exit;
    //        return $products;
    //    }
    //
    //    /**
    //     * @param integer $wirelessId
    //     * @param null    $orderId
    //     * @param array   $meta
    //     *
    //     * @return array|false
    //     * @throws \Exception
    //     */
    //    public function submit($wirelessId, $orderId = null, $meta = [])
    //    {
    //        $this->logger->addLog('Submitting order #%s', $orderId);
    //        $data = array_merge(
    //            [
    //                'correlation_id' => $orderId,
    //            ],
    //            $meta
    //        );
    //
    //        $transaction = $this->send(self::POST, sprintf('/product/%s/submit', $wirelessId), $data);
    //
    //        if ($transaction && $transaction->status === 'COMPLETED') {
    //            $this->logger->addLog('The order #%s has been completed successfully.', $orderId);
    //
    //            return $transaction;
    //        }
    //
    //        $message = sprintf('Invalid API response processing order #%s', $orderId);
    //        $this->logger->addErrorLog($message);
    //        throw new \Exception($message);
    //    }
    //
    //    /**
    //     * @param string $mobile
    //     *
    //     * @return array|false
    //     * @throws \Exception
    //     */
    //    public function accountInfo($mobile)
    //    {
    //        return $this->send(self::GET, sprintf('/account/%s', $mobile));
    //    }

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