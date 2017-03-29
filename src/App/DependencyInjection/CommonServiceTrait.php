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

namespace WooRefill\App\DependencyInjection;

use WooRefill\App\Logger\Logger;
use WooRefill\App\Twig\Template;
use WooRefill\Symfony\Component\DependencyInjection\ContainerAwareTrait;
use WooRefill\Symfony\Component\HttpFoundation\Request;

/**
 * Class CommonServiceTrait
 */
trait CommonServiceTrait
{
    use ContainerAwareTrait;

    /**
     * @param string $service #Service
     *
     * @return mixed
     */
    protected function get($service)
    {
        return $this->container->get($service);
    }

    /**
     * getRequest
     *
     * @return Request
     */
    protected function getRequest()
    {
        return $this->get('request');
    }

    /**
     * @return Logger
     */
    protected function getLogger()
    {
        return $this->get('logger');
    }

    /**
     * getTemplate
     *
     * @return Template
     */
    protected function getTemplate()
    {
        return $this->get('template');
    }

    /**
     * @param string $view #Template
     * @param array  $args
     *
     * @return string
     */
    protected function render($view, $args = [])
    {
        echo $this->getTemplate()->render($view, $args);
    }
}