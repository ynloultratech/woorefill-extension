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

namespace WooRefill\App;

use WooRefill\Symfony\Component\HttpFoundation\Session\SessionBagInterface;
use WooRefill\Symfony\Component\HttpFoundation\Session\SessionInterface;

class Session implements SessionInterface
{
    /**
     * @inheritDoc
     */
    public function start()
    {
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
       $this->getWCSession()->get_customer_id();
    }

    /**
     * @inheritDoc
     */
    public function setId($id)
    {

    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        $this->getWCSession()->get_customer_id();
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
    }

    /**
     * @inheritDoc
     */
    public function invalidate($lifetime = null)
    {
    }

    /**
     * @inheritDoc
     */
    public function migrate($destroy = false, $lifetime = null)
    {
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
    }

    /**
     * @inheritDoc
     */
    public function has($name)
    {
        $default = mt_rand();
        $result = $this->getWCSession()->get($name, $default);

        return ($result !== $default);
    }

    /**
     * @inheritDoc
     */
    public function get($name, $default = null)
    {
        return $this->getWCSession()->get($name, $default);
    }

    /**
     * @inheritDoc
     */
    public function set($name, $value)
    {
        $this->getWCSession()->set($name, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function all()
    {
        return $this->getWCSession()->get_session_data();
    }

    /**
     * @inheritDoc
     */
    public function replace(array $attributes)
    {
        foreach ($attributes as $attr => $value) {
            $this->set($attr, $value);
        }
    }

    /**
     * @inheritDoc
     */
    public function remove($name)
    {
        $this->getWCSession()->set($name, null);
    }

    /**
     * @inheritDoc
     */
    public function clear()
    {
        $this->getWCSession()->cleanup_sessions();
    }

    /**
     * @inheritDoc
     */
    public function isStarted()
    {
        return $this->getWCSession()->has_session();
    }

    /**
     * @inheritDoc
     */
    public function registerBag(SessionBagInterface $bag)
    {
    }

    /**
     * @inheritDoc
     */
    public function getBag($name)
    {
    }

    /**
     * @inheritDoc
     */
    public function getMetadataBag()
    {
    }

    /**
     * @return \WC_Session_Handler|\WC_Session
     */
    protected function getWCSession()
    {
        return \WooCommerce::instance()->session;
    }

}