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

namespace WooRefill\App\WPEventBridge;

use WooRefill\Symfony\Component\DependencyInjection\Reference;

/**
 * Class WPEvent
 */
class WPEvent
{
    const FILTER = 'wp_filter';
    const ACTION = 'wp_action';

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Reference
     */
    protected $reference;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var integer
     */
    protected $priority = 10;

    /**
     * WPEvent constructor.
     *
     * @param string    $type
     * @param string    $name
     * @param Reference $reference
     * @param string    $method
     * @param int       $priority
     */
    public function __construct($type, $name, Reference $reference, $method, $priority = 10)
    {
        $this->type = $type;
        $this->name = $name;
        $this->reference = $reference;
        $this->method = $method;
        $this->priority = $priority;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Reference
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param Reference $reference
     *
     * @return $this
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     *
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     *
     * @return $this
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }
}