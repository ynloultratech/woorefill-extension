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

namespace WooRefill\Admin\Page;

use WooRefill\App\Api\WooRefillApi;
use WooRefill\App\DependencyInjection\CommonServiceTrait;
use WooRefillSymfony\Component\DependencyInjection\ContainerAwareInterface;

if (!class_exists('WC_Settings_Page')) {
    include WC()->plugin_path().DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, ['includes', 'admin', 'settings', 'class-wc-settings-page.php']);
}

/**
 * Class Settings
 */
class Settings extends \WC_Settings_Page implements ContainerAwareInterface
{
    use CommonServiceTrait;

    /**
     * Settings constructor.
     */
    public function __construct()
    {
        $this->id = 'wireless';
        $this->label = __('Wireless');
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
                [
                    'title' => __('Pre-released updates'),
                    'type' => 'checkbox',
                    'desc' => __('Use pre-release updates if you\'re willing to report bugs or any problems that ocurr. <b style="color: orangered">WARNING: Non stable version may contains bugs.<b>'),
                    'id' => '_woorefill_prerelease',
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

    /**
     * @inheritDoc
     */
    public function save()
    {
        parent::save();

        $givenApiKey = $this->getRequest()->get('_woorefill_api_key');

        /** @var WooRefillApi $refillApi */
        $refillApi = $this->get('refill_api');
        $refillApi->setApiKey($givenApiKey);
        try {
            $carriers = $refillApi->getCarriers();
            \WC_Admin_Settings::add_message('Your API key has been verified and is VALID.');

        } catch (\Exception $e) {
            $errorCode = $e->getCode();
            if ($errorCode > 400 && $errorCode < 500) {
                \WC_Admin_Settings::add_error('Your API key is not valid, please verify or get a new API key.');
            }
        }
    }
}