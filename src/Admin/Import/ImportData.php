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

namespace WooRefill\Admin\Import;

/**
 * Class ImportData
 */
class ImportData
{
    /**
     * @var array
     */
    private $carrierId;

    /**
     * Array of products id to import
     *
     * @var array
     */
    private $products;

    /**
     * @var boolean
     */
    private $createCarrier = true;

    /**
     * @var string
     */
    private $newCarrierName;

    /**
     * @var \WP_Term
     */
    private $existentCarrier;

    /**
     * @var boolean
     */
    private $createCategory = false;

    /**
     * @var string
     */
    private $newCategoryName;

    /**
     * @var \WP_Term
     */
    private $newCategoryParent;

    /**
     * @var \WP_Term
     */
    private $existentCategory;

    /**
     * @var \WP_Term
     */
    private $status;

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
     * @return boolean
     */
    public function isCreateCarrier()
    {
        return $this->createCarrier;
    }

    /**
     * @param boolean $createCarrier
     *
     * @return $this
     */
    public function setCreateCarrier($createCarrier)
    {
        $this->createCarrier = $createCarrier;

        return $this;
    }

    /**
     * @return string
     */
    public function getNewCarrierName()
    {
        return $this->newCarrierName;
    }

    /**
     * @param string $newCarrierName
     *
     * @return $this
     */
    public function setNewCarrierName($newCarrierName)
    {
        $this->newCarrierName = $newCarrierName;

        return $this;
    }

    /**
     * @return \WP_Term
     */
    public function getExistentCarrier()
    {
        return $this->existentCarrier;
    }

    /**
     * @param \WP_Term $existentCarrier
     *
     * @return $this
     */
    public function setExistentCarrier($existentCarrier)
    {
        $this->existentCarrier = $existentCarrier;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isCreateCategory()
    {
        return $this->createCategory;
    }

    /**
     * @param boolean $createCategory
     *
     * @return $this
     */
    public function setCreateCategory($createCategory)
    {
        $this->createCategory = $createCategory;

        return $this;
    }

    /**
     * @return string
     */
    public function getNewCategoryName()
    {
        return $this->newCategoryName;
    }

    /**
     * @param string $newCategoryName
     *
     * @return $this
     */
    public function setNewCategoryName($newCategoryName)
    {
        $this->newCategoryName = $newCategoryName;

        return $this;
    }

    /**
     * @return \WP_Term
     */
    public function getNewCategoryParent()
    {
        return $this->newCategoryParent;
    }

    /**
     * @param \WP_Term $newCategoryParent
     *
     * @return $this
     */
    public function setNewCategoryParent($newCategoryParent)
    {
        $this->newCategoryParent = $newCategoryParent;

        return $this;
    }

    /**
     * @return \WP_Term
     */
    public function getExistentCategory()
    {
        return $this->existentCategory;
    }

    /**
     * @param \WP_Term $existentCategory
     *
     * @return $this
     */
    public function setExistentCategory($existentCategory)
    {
        $this->existentCategory = $existentCategory;

        return $this;
    }

    /**
     * @return \WP_Term
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param \WP_Term $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }
}