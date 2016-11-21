<?php

/*
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 *
 * @author YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package woorefill-extension
 * @version 1.0-alpha
 */

if ( ! defined('ABSPATH')) {
    exit;
}

if ( ! class_exists('WC_Settings_Page')) {
    include WC()->plugin_path().DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, ['includes', 'admin', 'settings', 'class-wc-settings-page.php']);
}

class WC_Settings_Wireless extends WC_Settings_Page
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->id = 'wireless';
        $this->label = __('Wireless');

        add_filter('woocommerce_settings_tabs_array', [$this, 'add_settings_page'], 40);
        add_action('woocommerce_settings_'.$this->id, [$this, 'output']);
        add_action('woocommerce_settings_save_'.$this->id, [$this, 'save']);
    }

    /**
     * Get settings array.
     *
     * @return array
     */
    public function get_settings($current_section = '')
    {
        $settings = apply_filters(
            'woocommerce_wireless_settings',
            [
                //                [
                //                    'title' => __('Wireless Settings'),
                //                    'type'  => 'title',
                //                    'desc'  => '',
                //                    'id'    => 'woorefill_wireless_settings',
                //                ],
                //                ['type' => 'sectionend', 'id' => 'woorefill_wireless_settings'],
                [
                    'title' => __('Refill Service'),
                    'type' => 'title',
                    'desc' => '',
                    'id' => 'woorefill_api_service',
                ],
                [
                    'title' => __('API Key'),
                    'type' => 'text',
                    'desc' => __('API Key to communicate with service to make refills'),
                    'id' => '_woorefill_api_key',
                    'css' => 'width: 300px;',
                ],
                'debug' => [
                    'title' => __('Debug Log'),
                    'type' => 'checkbox',
                    'label' => __('Enable logging'),
                    'default' => 'no',
                    'id' => '_woorefill_log',
                    'desc' => sprintf(__('Log WooRefill events, such as API request and responses, inside <code>%s</code>'), wc_get_log_file_path('woorefill')),
                ],
                ['type' => 'sectionend', 'id' => 'woorefill_api_service'],
            ]
        );

        return $settings;
    }
}

return new WC_Settings_Wireless();