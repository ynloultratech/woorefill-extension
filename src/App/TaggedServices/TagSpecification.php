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

namespace WooRefill\App\TaggedServices;

use WooRefillSymfony\Component\DependencyInjection\ContainerInterface;
use WooRefillSymfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use WooRefillSymfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class TagSpecification
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * TagSpecification constructor.
     *
     * @param string             $id
     * @param string             $name
     * @param array              $attributes
     * @param ContainerInterface $container
     */
    public function __construct($id, $name, array $attributes, ContainerInterface $container = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->attributes = $attributes;
        $this->container = $container;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @throws ServiceCircularReferenceException
     * @throws ServiceNotFoundException
     *
     * @return mixed
     */
    public function getService()
    {
        return $this->container->get($this->getId());
    }
}
