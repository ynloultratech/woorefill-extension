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
use WooRefillJMS\Serializer\Annotation\Type;

class PaginatedCollection
{
    /**
     * @var int
     *
     * @Type("integer")
     */
    public $page = 1;

    /**
     * @var int
     *
     * @Type("integer")
     */
    public $pages = 1;

    /**
     * @var int
     *
     * @Type("integer")
     */
    public $limit;

    /**
     * @var int
     *
     * @Type("integer")
     */
    public $total;

    /**
     * @var object[]|array
     * @Exclude()
     */
    public $items;
}