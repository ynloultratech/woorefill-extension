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

namespace WooRefill\Admin\DependencyInjection;

use WooRefillSymfony\Component\Config\FileLocator;
use WooRefillSymfony\Component\DependencyInjection\ContainerBuilder;
use WooRefillSymfony\Component\DependencyInjection\Extension\Extension;
use WooRefillSymfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Class AdminExtension
 */
class AdminExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('controllers.xml');
        $loader->load('forms.xml');
    }
}