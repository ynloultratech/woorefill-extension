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

use WooRefillJMS\Serializer\Annotation\SerializedName;
use WooRefillJMS\Serializer\Annotation\Type;

class ValidationError
{
    /**
     * @var string
     *
     * @Type("string")
     */
    protected $message;

    /**
     * @var string
     *
     * @Type("string")
     */
    protected $code;

    /**
     * @var string
     * @Type("string")
     */
    protected $property;

    /**
     * @var string
     *
     * @SerializedName("invalidValue")
     * @Type("string")
     */
    protected $invalidValue;

    /**
     * ValidationError constructor.
     *
     * @param string $message
     * @param string $property
     * @param string $invalidValue
     * @param string $code
     */
    public function __construct($message = null, $property = null, $invalidValue = null, $code = null)
    {
        $this->message = $message;
        $this->code = $code;
        $this->property = $property;
        $this->invalidValue = $invalidValue;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * @param string $property
     */
    public function setProperty($property)
    {
        $this->property = $property;
    }

    /**
     * @return string
     */
    public function getInvalidValue()
    {
        return $this->invalidValue;
    }

    /**
     * @param string $invalidValue
     */
    public function setInvalidValue($invalidValue)
    {
        $this->invalidValue = $invalidValue;
    }
}