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

use WooRefill\JMS\Serializer\Annotation\MaxDepth;
use WooRefill\JMS\Serializer\Annotation\SerializedName;
use WooRefill\JMS\Serializer\Annotation\Type;

class Transaction
{
    /**
     * @var integer
     * @Type("integer")
     */
    public $id;

    /**
     * @var \DateTime
     * @Type("DateTime")
     */
    public $date;

    /**
     * @var string
     * @Type("string")
     */
    public $status;

    /**
     * @var string
     *
     * @Type("string")
     * @SerializedName("correlationId")
     */
    public $correlationId;

    /**
     * @var Product
     * @Type("WooRefill\App\Model\Product")
     * @MaxDepth(1)
     */
    public $product;

    /**
     * @var array
     *
     * @Type("array")
     */
    public $inputs;

    /**
     * @var array
     *
     * @Type("array<string, string>")
     */
    public $response;

    /**
     * @var string
     *
     * @SerializedName("errorCode")
     * @Type("string")
     */
    public $errorCode;

    /**
     * @var string
     *
     * @SerializedName("responseMessage")
     * @Type("string")
     */
    public $responseMessage;
}