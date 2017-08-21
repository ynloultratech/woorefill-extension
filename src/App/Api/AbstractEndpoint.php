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

use WooRefill\App\Model\PaginatedCollection;

abstract class AbstractEndpoint
{
    /**
     * @var WooRefillApi
     */
    protected $api;

    /**
     * @var string
     */
    protected $path;

    /**
     * AbstractEndpoint constructor.
     *
     * @param WooRefillApi $api
     * @param string       $path
     */
    public function __construct(WooRefillApi $api, $path)
    {
        $this->api = $api;
        $this->path = $path;
    }


    /**
     * @param null  $q
     * @param int   $page
     * @param int   $limit
     * @param array $filters
     *
     * @return PaginatedCollection
     */
    public function getList($q = null, $page = 1, $limit = 30, $filters = [])
    {
        $params = array_merge(
            [
                'q' => $q,
                'page' => $page,
                'limit' => $limit,
            ],
            $filters
        );

        $records = $this->requestGet('', $params);

        if (isset($records['items'])) {
            /** @var PaginatedCollection $collection */
            $collection = $this->deserialize($records, 'WooRefill\App\Model\PaginatedCollection');
            $collection->items = $this->deserialize($records['items'], 'array<'.$this->getModeClass().'>');

            return $collection;
        }

        throw new \RuntimeException('Invalid API response');
    }

    /**
     * @param $sku
     *
     * @return object
     */
    public function get($sku)
    {
        $record = $this->requestGet('/'.$sku);

        if ($record) {
            return $this->deserialize($record, $this->getModeClass());
        }

        throw new \RuntimeException('Invalid API response');
    }

    /**
     * send
     *
     * @param                     $method
     * @param                     $path
     * @param array               $params
     * @param string|array|object $body
     *
     * @return array
     * @throws \Exception
     */
    protected function request($method, $path, array $params = [], $body = null)
    {
        return $this->api->request($method, $this->path.$path, $params, $body);
    }

    /**
     * @param string $path
     * @param array  $params
     * @param null   $body
     *
     * @return array
     */
    protected function requestGet($path, array $params = [], $body = null)
    {
        return $this->request('get', $path, $params, $body);
    }

    /**
     * @param string $path
     * @param null   $body
     * @param array  $params
     *
     * @return array
     */
    protected function requestPost($path, $body = null, array $params = [])
    {
        return $this->request('post', $path, $params, $body);
    }

    /**
     * @param string $data
     * @param string $type
     *
     * @return mixed
     */
    protected function deserialize($data, $type)
    {
        if (is_array($data)) {
            return $this->api->getSerializer()->fromArray($data, $type);
        }

        return $this->api->getSerializer()->deserialize($data, $type, 'json');
    }

    /**
     * @param object $data
     *
     * @return array
     */
    protected function toArray($data)
    {
        return $this->api->getSerializer()->toArray($data);
    }

    abstract protected function getModeClass();
}