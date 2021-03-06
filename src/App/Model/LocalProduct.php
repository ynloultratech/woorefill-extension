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

use WooRefillJMS\Serializer\Annotation\Type;

class LocalProduct
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
     */
    public $slug;

    /**
     * @var string
     *
     * @Type("integer")
     */
    public $sku;

    /**
     * @var boolean
     *
     * @Type("boolean")
     */
    public $enabled;
}