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

namespace WooRefill\Shop\DependencyInjection;

use WooRefill\Symfony\Component\Config\FileLocator;
use WooRefill\Symfony\Component\DependencyInjection\ContainerBuilder;
use WooRefill\Symfony\Component\DependencyInjection\Extension\Extension;
use WooRefill\Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Class ShopExtension
 */
class ShopExtension extends Extension
{

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
    }
}