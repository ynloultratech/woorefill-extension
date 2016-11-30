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

namespace WooRefill\App;

use WooRefill\Admin\DependencyInjection\AdminExtension;
use WooRefill\App\DependencyInjection\AppExtension;
use WooRefill\Shop\DependencyInjection\ShopExtension;
use WooRefill\Symfony\Component\DependencyInjection\Container;
use WooRefill\Symfony\Component\DependencyInjection\ContainerBuilder;
use WooRefill\Symfony\Component\DependencyInjection\Extension\Extension;
use WooRefill\Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use WooRefill\Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use WooRefill\App\TaggedServices\TaggedServicesCompilerPass;

/**
 * Class Container
 */
class Kernel
{
    /**
     * @var Container
     */
    protected static $container;

    /**
     * @var ContainerAwareEventDispatcher
     */
    protected static $eventDispatcher;

    public static function init()
    {
        if (self::$container) {
            return;
        }

        self::$container = new ContainerBuilder();

        /** @var Extension[] $extensions */
        $extensions = [
            new AppExtension(),
            new AdminExtension(),
            new ShopExtension(),
        ];
        foreach ($extensions as $extension) {
            $extension->load([], self::$container);
        }

        self::$eventDispatcher = new ContainerAwareEventDispatcher(self::$container);
        self::$container->addCompilerPass(new RegisterListenersPass());
        self::$container->addCompilerPass(new TaggedServicesCompilerPass());
        self::$container->compile();

        Kernel::get('event_dispatcher');//initialize
    }

    /**
     * @return ContainerAwareEventDispatcher
     */
    public static function getEventDispatcher()
    {
        if (!self::$eventDispatcher) {
            Kernel::init();
        }

        return self::$eventDispatcher;
    }

    /**
     * @return Container
     */
    public static function getContainer()
    {
        if (!self::$container) {
            Kernel::init();
        }

        return self::$container;
    }

    /**
     * @param $service
     *
     * @return object #Service
     */
    public static function get($service)
    {
        return self::getContainer()->get($service);
    }

    /**
     * @param string $name
     *
     * @return string|mixed
     */
    public static function getParameter($name)
    {
        return self::getContainer()->getParameter($name);
    }
}