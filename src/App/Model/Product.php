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

namespace WooRefill\App\Model;

use WooRefill\JMS\Serializer\Annotation\Exclude;
use WooRefill\JMS\Serializer\Annotation\SerializedName;
use WooRefill\JMS\Serializer\Annotation\Type;

class Product
{
    /**
     * @var integer
     *
     * @Type("integer")
     */
    public $id;

    /**
     * @var string
     *
     * @Type("string")
     */
    public $name;

    /**
     * @var float
     *
     * @Type("float")
     */
    public $amount = 0;

    /**
     * @var float
     *
     * @Type("float")
     * @SerializedName("minAmount")
     */
    public $minAmount = 0;

    /**
     * @var float
     *
     * @Type("float")
     * @SerializedName("maxAmount")
     */
    public $maxAmount = 0;

    /**
     * @var boolean
     *
     * @Type("boolean")
     * @SerializedName("variableAmount")
     */
    public $variableAmount = false;

    /**
     * @var float
     *
     * @Type("float")
     * @SerializedName("discountRate")
     */
    public $discountRate = 0;

    /**
     * @var string
     *
     * @Type("string")
     */
    public $type;

    /**
     * @var boolean
     *
     * @Type("boolean")
     * @SerializedName("allowDecimal")
     */
    public $allowDecimal = true;

    /**
     * @var Carrier
     *
     * @Type("WooRefill\App\Model\Carrier")
     */
    public $carrier;

    /**
     * @var LocalProduct
     * @Exclude()
     */
    public $localProduct;

    /**
     * @var array
     * @Type("array<string,array>")
     */
    public $inputs;

}