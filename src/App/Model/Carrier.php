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

use WooRefillJMS\Serializer\Annotation\Exclude;
use WooRefillJMS\Serializer\Annotation\SerializedName;
use WooRefillJMS\Serializer\Annotation\Type;

class Carrier
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
     * @var string
     *
     * @Type("string")
     * @SerializedName("countryCode")
     */
    public $countryCode;

    /**
     * @var string
     *
     * @Type("string")
     * @SerializedName("currencyCode")
     */
    public $currencyCode;

    /**
     * @var string
     *
     * @Type("string")
     * @SerializedName("internationalCode")
     */
    public $internationalCode;

    /**
     * @var string
     *
     * @Type("string")
     * @SerializedName("internationalCode")
     */
    public $phoneLength;

    /**
     * @var Operator
     *
     * @Type("WooRefill\App\Model\Operator")
     */
    public $operator;

    /**
     * @var LocalCarrier
     * @Exclude()
     */
    public $localCarrier;

}