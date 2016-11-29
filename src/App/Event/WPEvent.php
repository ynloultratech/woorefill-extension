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

namespace WooRefill\App\Event;

use WooRefill\Symfony\Component\EventDispatcher\Event as BaseEvent;

/**
 * Class WPEvent
 */
class WPEvent extends BaseEvent
{
    protected $args;

    /**
     * WPEvent constructor.
     */
    public function __construct($args = [])
    {
        $this->args = $args;
    }

    /**
     * @return mixed
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * @param mixed $args
     *
     * @return $this
     */
    public function setArgs($args)
    {
        $this->args = $args;

        return $this;
    }
}