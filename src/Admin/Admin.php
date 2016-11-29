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

namespace WooRefill\Admin;

use WooRefill\App\Asset\AssetRegister;
use WooRefill\App\DependencyInjection\CommonServiceTrait;
use WooRefill\App\TaggedServices\TaggedServices;
use WooRefill\App\TaggedServices\TagSpecification;
use WooRefill\Symfony\Component\DependencyInjection\ContainerAwareInterface;
use WooRefill\Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class Admin
 */
class Admin implements ContainerAwareInterface
{
    use CommonServiceTrait;

    /**
     * Build the admin menu
     */
    public function menu()
    {
        /** @var TaggedServices $taggedServices */
        $taggedServices = $this->container->get('tagged_services');

        //initialize all admin menus
        $specifications = $taggedServices->findTaggedServices('admin_menu');
        foreach ($specifications as $specification) {
            $this->addServiceTag($specification);
        }

        //initialize all standalone controller actions
        $specifications = $taggedServices->findTaggedServices('controller_action');
        foreach ($specifications as $specification) {
            $this->addServiceTag($specification);
        }
    }

    /**
     * addServiceTag
     *
     * @param TagSpecification $specification
     */
    public function addServiceTag(TagSpecification $specification)
    {
        $parent = isset($specification->getAttributes()['parent']) ? $specification->getAttributes()['parent'] : null;
        $title = isset($specification->getAttributes()['title']) ? $specification->getAttributes()['title'] : null;
        $slug = isset($specification->getAttributes()['slug']) ? $specification->getAttributes()['slug'] : null;
        if (!$slug) {
            $slug = strtolower(str_replace(' ', '_', $title));
        }

        if ($parent) {
            $this->addSubMenu(
                $parent,
                $title,
                $slug,
                [$specification->getService(), $specification->getAttributes()['method']]
            );
        }
    }

    /**
     * checkAPIKey
     */
    public function checkAPIKey()
    {
        $apiKey = $this->container->getParameter('api_key');
        if (empty($apiKey) && !(isset($_GET['page'], $_GET['tab']) && 'wc-settings' === $_GET['page'] && 'wireless' === $_GET['tab'])) {
            $message = sprintf('WooRefill is almost ready. To get started, <a href="%s">set your WooRefill API Key</a>.','/wp-admin/admin.php?page=wc-settings&tab=wireless');
            $this->render(
                '@Admin/notice.html.twig',
                [
                    'type'=>'warning',
                    'message'=>$message
                ]
            );
        }
    }

    /**
     * registerAssets
     */
    public function registerAssets()
    {
        /** @var AssetRegister $register */
        $register = $this->container->get('asset_register');
        $register->enqueueScript('datatables', '/public/admin/js/jquery.dataTables.min.js');
        $register->enqueueScript('woorefill_admin_core', '/public/admin/js/woorefill_admin.core.js');
        $register->enqueueScript('jquery_validate', '/public/admin/js/jquery.validate.min.js');
        $register->enqueueScript('jquery_validate', '/public/admin/js/jquery.validate.min.js');
        $register->enqueueScript('form_toggle', '/public/admin/js/jquery-form-toggle.js');
        $register->enqueueStyle('datatables_css', '/public/admin/css/jquery.dataTables.min.css');
        $register->enqueueStyle('woorefill_admin_core', '/public/admin/css/woorefill_admin.core.css');
    }

    /**
     * addSubMenu
     *
     * @param $parent
     * @param $title
     * @param $slug
     * @param $action
     */
    protected function addSubMenu($parent, $title, $slug, $action)
    {
        add_submenu_page($parent, $title, $title, 'manage_woocommerce', $slug, $action);
    }
}