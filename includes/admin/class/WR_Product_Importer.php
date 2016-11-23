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

class WR_Product_Importer
{
    /**
     * Carrier id
     *
     * @var array
     */
    protected $carrierId;

    /**
     * List of products ids to import
     *
     * @var array
     */
    protected $products;

    /**
     * @var WP_Term
     */
    protected $carrier;

    /**
     * @var string
     */
    protected $carrierName;

    /**
     * @var WP_Term
     */
    protected $category;

    /**
     * @var string
     */
    protected $categoryName;

    /**
     * @var string
     */
    protected $status = 'publish';

    /**
     * @return array
     */
    public function getCarrierId()
    {
        return $this->carrierId;
    }

    /**
     * @param array $carrierId
     *
     * @return $this
     */
    public function setCarrierId($carrierId)
    {
        $this->carrierId = $carrierId;

        return $this;
    }

    /**
     * @return array
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param array $products
     *
     * @return $this
     */
    public function setProducts($products)
    {
        $this->products = $products;

        return $this;
    }

    /**
     * @return WP_Term
     */
    public function getCarrier()
    {
        return $this->carrier;
    }

    /**
     * @param WP_Term $carrier
     *
     * @return $this
     */
    public function setCarrier($carrier)
    {
        $this->carrier = $carrier;

        return $this;
    }

    /**
     * @return string
     */
    public function getCarrierName()
    {
        return $this->carrierName;
    }

    /**
     * @param string $carrierName
     *
     * @return $this
     */
    public function setCarrierName($carrierName)
    {
        $this->carrierName = $carrierName;

        return $this;
    }

    /**
     * @return WP_Term
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param WP_Term $category
     *
     * @return $this
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return string
     */
    public function getCategoryName()
    {
        return $this->categoryName;
    }

    /**
     * @param string $categoryName
     *
     * @return $this
     */
    public function setCategoryName($categoryName)
    {
        $this->categoryName = $categoryName;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function import()
    {
        $this->validate();
        $apiProducts = WooRefillAPI::getProducts($this->getCarrierId());
        if ( ! is_array($apiProducts) || ! $apiProducts) {
            throw new \Exception('Unknown error, can\'t import products.');
        }

        if ( ! $this->getCategory() && $this->getCarrierName()) {
            $response = wp_insert_term($this->getCategoryName(), 'product_cat');
            if ($response instanceof WP_Error) {
                throw new \LogicException($response->get_error_message());
            }

            if (is_array($response)) {
                $id = $response['term_id'];
                $this->setCategory(get_term($id));
            }
        }


        if ( ! $this->getCarrier() && $this->getCarrierName()) {
            $response = wp_insert_term($this->carrierName, 'product_cat', ['parent' => $this->category->term_id]);
            if ($response instanceof WP_Error) {
                throw new \LogicException($response->get_error_message());
            }

            if (is_array($response)) {
                $id = $response['term_id'];
                add_term_meta($id, 'wireless_carrier', 1);
                $this->setCarrier(get_term($id));
            }
        }

        if (is_array($apiProducts)) {
            foreach ($apiProducts as $product) {
                if (in_array($product['id'], $this->products)) {
                    $post_id = wp_insert_post(
                        [
                            'post_title' => $product['name'],
                            'post_status' => $this->getStatus(),
                            'post_type' => "product",
                        ]
                    );
                    wp_set_object_terms($post_id, $this->getCarrier()->term_id, 'product_cat');
                    wp_set_object_terms($post_id, 'wireless', 'product_type');

                    update_post_meta($post_id, '_wireless_product_id', $product['id']);
                    update_post_meta($post_id, '_regular_price', $product['amount']);
                    update_post_meta($post_id, '_price', $product['amount']);
                    update_post_meta($post_id, '_sale_price', $product['amount']);
                    update_post_meta($post_id, '_visibility', 'visible');
                    update_post_meta($post_id, '_stock_status', 'instock');
                    update_post_meta($post_id, 'total_sales', '0');
                    update_post_meta($post_id, '_downloadable', 'no');
                    update_post_meta($post_id, '_virtual', 'yes');
                    update_post_meta($post_id, '_sale_price', '');
                    update_post_meta($post_id, '_purchase_note', '');
                    update_post_meta($post_id, '_featured', 'no');
                    update_post_meta($post_id, '_weight', '');
                    update_post_meta($post_id, '_length', '');
                    update_post_meta($post_id, '_width', '');
                    update_post_meta($post_id, '_height', '');
                    update_post_meta($post_id, '_sku', '');
                    update_post_meta($post_id, '_product_attributes', []);
                    update_post_meta($post_id, '_sale_price_dates_from', '');
                    update_post_meta($post_id, '_sale_price_dates_to', '');
                    update_post_meta($post_id, '_sold_individually', '');
                    update_post_meta($post_id, '_manage_stock', 'no');
                    update_post_meta($post_id, '_backorders', 'no');
                    update_post_meta($post_id, '_stock', '');
                }
            }
        }
    }

    /**
     * validate
     */
    protected function validate()
    {
        if ( ! $this->getCarrier() && wr_carrier_name_exists($this->getCarrierName())) {
            throw new LogicException(sprintf('Already exist a carrier with the name "%s"', $this->getCarrierName()));
        }

        if ( ! $this->getCarrier() && ! $this->getCategory() && wr_non_carrier_category_exists($this->getCategoryName())) {
            throw new LogicException(sprintf('Already exist a category with the name "%s"', $this->getCategoryName()));
        }
    }
}