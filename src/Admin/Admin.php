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

namespace WooRefill\Admin;

use WooRefill\App\Asset\AssetRegister;
use WooRefill\App\DependencyInjection\CommonServiceTrait;
use WooRefill\App\TaggedServices\TaggedServices;
use WooRefill\App\TaggedServices\TagSpecification;
use WooRefillSymfony\Component\DependencyInjection\ContainerAwareInterface;

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
        $iconUrl = isset($specification->getAttributes()['icon']) ? $specification->getAttributes()['icon'] : null;
        $position = isset($specification->getAttributes()['position']) ? $specification->getAttributes()['position'] : null;

        if (!$slug) {
            $slug = strtolower(str_replace(' ', '_', $title));
        }

        $method = isset($specification->getAttributes()['method']) ? $specification->getAttributes()['method'] : null;

        if ($parent) {
            $this->addSubMenu(
                $parent,
                $title,
                $slug,
                [$specification->getService(), $method]
            );
        } else {
            $this->addMenu(
                $title,
                $slug,
                $method ? [$specification->getService(), $method] : null,
                $iconUrl,
                $position
            );
        }
    }

    /**
     * checkAPIKey
     */
    public function checkAPIKey()
    {
        $apiKey = $this->container->getParameter('api_key');
        if (empty($apiKey) && (!isset($_GET['page']) || $_GET['page'] !== 'woorefill-settings')) {
            $message = sprintf('WooRefill is almost ready. To get started, <a href="%s">set your WooRefill API Key</a>.', admin_url().'admin.php?page=woorefill-settings');
            $this->render(
                '@Admin/notice.html.twig',
                [
                    'type' => 'warning',
                    'message' => $message,
                ]
            );
        }
    }

    public function checkLogsOn()
    {
        $logsEnabled = $this->container->getParameter('enable_logs');
        if ($logsEnabled && (!isset($_GET['page']) || $_GET['page'] !== 'woorefill-settings')) {
            $message = sprintf('You have WooRefill Debug Logs ENABLED, this may reduce the system performance, <a href="%s">DISABLE LOGS</a>. (<small> Leave ENABLED if you has problems with this plugin, in that case please contact <a href="mailto:support@ynolultratech.com">YnloUltratech</a></small>)', admin_url().'admin.php?page=woorefill-settings');
            $this->render(
                '@Admin/notice.html.twig',
                [
                    'type' => 'error',
                    'message' => $message,
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
        $register->enqueueScript('form_toggle', '/public/admin/js/jquery-form-toggle.js');
        $register->enqueueStyle('datatables_css', '/public/admin/css/jquery.dataTables.min.css');
        $register->enqueueStyle('woorefill_admin_core', '/public/admin/css/woorefill_admin.core.css', ['woocommerce_admin_styles']);
        $register->enqueueStyle('intlTelInput', '/public/int-tel-input/css/intlTelInput.css'); //use flags

        //register TinyMce plugin
        add_filter(
            "mce_external_plugins",
            function ($plugin_array) {
                $plugin = $this->container->getParameter('plugin_file');
                $plugin_array['woorefill'] = plugin_dir_url($plugin).'public/admin/js/woorefill-tinymce-plugin.js';

                return $plugin_array;
            }
        );
        add_filter(
            'mce_buttons',
            function ($buttons) {
                array_push($buttons, 'quick_refill');

                return $buttons;
            }
        );
    }

    /**
     * @param $parent
     * @param $title
     * @param $slug
     * @param $action
     */
    protected function addSubMenu($parent, $title, $slug, $action)
    {
        add_submenu_page($parent, $title, $title, 'manage_woocommerce', $slug, $action);
    }

    protected function addMenu($title, $slug, $action, $iconUrl, $position)
    {
        add_menu_page($title, $title, 'manage_woocommerce', $slug, $action, $iconUrl, $position);
    }
}