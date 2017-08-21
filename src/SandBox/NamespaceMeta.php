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

namespace WooRefill\SandBox;

class NamespaceMeta
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array|string[]
     */
    protected $paths = [];

    /**
     * @var int
     */
    protected $psr = 0;

    /**
     * NamespaceMeta constructor.
     *
     * @param       $name
     * @param array $paths
     * @param int   $psr
     */
    public function __construct($name, array $paths, $psr)
    {
        $this->name = $name;
        $this->paths = $paths;
        $this->psr = $psr;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * @return int
     */
    public function getPsr()
    {
        return $this->psr;
    }

    public function __toString()
    {
        return (string) $this->name;
    }
}