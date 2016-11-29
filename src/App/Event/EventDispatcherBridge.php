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

use WooRefill\Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use WooRefill\Symfony\Component\EventDispatcher\Event;

/**
 * Class EventDispatcherBridge
 */
class EventDispatcherBridge extends ContainerAwareEventDispatcher
{
//    const WP_FILTER_PREFIX = 'wpf__';
//    const WP_ACTION_PREFIX = 'wpa__';
//    const ADD_FILTER = 'add_filter';
//    const ADD_ACTION = 'add_action';
//
//    /**
//     * {@inheritdoc}
//     */
//    public function addListener($eventName, $listener, $priority = 0)
//    {
//        parent::addListener($eventName, $listener, $priority);
//        $this->registerWPCallback($eventName, $priority);
//    }
//
//    /**
//     * {@inheritdoc}
//     */
//    public function addListenerService($eventName, $callback, $priority = 0)
//    {
//        parent::addListenerService($eventName, $callback, $priority);
//        $this->registerWPCallback($eventName, $priority);
//    }
//
//    /**
//     * registerWPCallback
//     *
//     * @param     $eventName
//     * @param int $priority
//     */
//    protected function registerWPCallback($eventName, $priority = 0)
//    {
//        if (strpos($eventName, self::WP_ACTION_PREFIX) === 0) {
//            $method = self::ADD_ACTION;
//            $wpName = str_replace(self::WP_ACTION_PREFIX, '', $eventName);
//        } elseif (strpos($eventName, self::WP_FILTER_PREFIX) === 0) {
//            $method = self::ADD_FILTER;
//            $wpName = str_replace(self::WP_FILTER_PREFIX, '', $eventName);
//        } else {
//            return;
//        }
//        $priority = $priority ? $priority : 10;//wp default priority is 10
//        $method(
//            $wpName,
//            function () use ($eventName) {
//                $event = new WPEvent(func_get_args());
//
//                return $this->dispatch($eventName, $event);
//            },
//            $priority,
//            15
//        );
//    }
//
//    /**
//     * {@inheritdoc}
//     */
//    public function dispatch($eventName, Event $event = null)
//    {
//        if (null === $event) {
//            $event = new Event();
//        }
//
//        if ($listeners = $this->getListeners($eventName)) {
//            $result = $this->doDispatch($listeners, $eventName, $event);
//            //added support for wp filters
//            if (strpos($eventName, self::WP_FILTER_PREFIX) === 0) {
//                return $result;
//            }
//        }
//
//        return $event;
//    }
//
//    /**
//     * {@inheritdoc}
//     */
//    protected function doDispatch($listeners, $eventName, Event $event)
//    {
//        if ($event instanceof WPEvent) {
//            $data = $event->getArgs();
//            foreach ($listeners as $listener) {
//                if ($event->isPropagationStopped()) {
//                    break;
//                }
//                $data = call_user_func_array($listener, $data);
//            }
//            if (strpos($eventName, self::WP_FILTER_PREFIX) === 0) {
//                return $data;
//            }
//        } else {
//            parent::doDispatch($listeners, $eventName, $event);
//        }
//    }
}