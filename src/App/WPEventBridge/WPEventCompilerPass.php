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

namespace WooRefill\App\WPEventBridge;

use WooRefill\App\Kernel;
use WooRefill\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use WooRefill\Symfony\Component\DependencyInjection\ContainerBuilder;
use WooRefill\Symfony\Component\DependencyInjection\Reference;

/**
 * This compiler pass convert some tags like wp_filter and wp_action
 * in WP actions and filter to call specific service actions
 */
class WPEventCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('wp_event_manager')) {
            return;
        }

        $events = [
            WPEvent::ACTION,
            WPEvent::FILTER,
        ];

        foreach ($events as $event) {
            $taggedServices = $container->findTaggedServiceIds($event);

            foreach ($taggedServices as $id => $attributes) {
                foreach ($attributes as $attribute) {
                    $reference = new Reference($id);
                    $priority = isset($attribute['priority']) ? $attribute['priority'] : 10;
                    $name = $attribute['tag'];
                    $method = $attribute['method'];
                    $wpEvent = new WPEvent($event, $name, $reference, $method, $priority);
                    $this->registerWPEvent($wpEvent);
                }
            }
        }
    }

    /**
     * registerWPEvent
     *
     * @param WPEvent $event
     */
    public function registerWPEvent(WPEvent $event)
    {
        $function = function () use ($event) {
            $service = Kernel::getContainer()->get($event->getReference());
            try {
                $result = call_user_func_array([$service, $event->getMethod()], func_get_args());
            } catch (\Exception $e) {
                Kernel::get('logger')->addErrorLog($e);
                $result = new \WP_Error($e->getCode(), $e->getMessage());
            }

            return $result;
        };

        if ($event->getType() === WPEvent::ACTION) {
            add_action($event->getName(), $function, $event->getPriority(), 20);
        }

        if ($event->getType() === WPEvent::FILTER) {
            add_filter($event->getName(), $function, $event->getPriority(), 20);
        }
    }
}