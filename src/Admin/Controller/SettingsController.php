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

namespace WooRefill\Admin\Controller;

use WooRefill\App\Api\WooRefillApi;
use WooRefill\App\Controller\Controller;
use WooRefillSymfony\Component\Form\FormError;
use WooRefillSymfony\Component\Form\FormInterface;

class SettingsController extends Controller
{
    public function settingsAction()
    {

        $settingsForm = $this->createSettingsForm();
        $advancedSettingsForm = $this->createAdvancedSettingsForm();

        $saved = false;
        if ($settingsForm->isSubmitted() && $advancedSettingsForm->isSubmitted()) {

            $data = array_merge($settingsForm->getData(), $advancedSettingsForm->getData());

            $givenApiKey = $data['_woorefill_api_key'];

            //validate api key
            if ($givenApiKey) {
                /** @var WooRefillApi $refillApi */
                $refillApi = $this->get('refill_api');
                $refillApi->setApiKey($givenApiKey);
                try {
                    $carriers = $refillApi->getCarriers()->getList(null, 1, 1);
                } catch (\Exception $e) {
                    $errorCode = $e->getCode();
                    if ($errorCode > 400 && $errorCode < 500) {
                        $settingsForm->addError(new FormError('Your API key is not valid, please verify or get a new API key.'));
                    }
                }
            }

            if ($settingsForm->isValid() && $advancedSettingsForm->isValid()) {
                foreach ($data as $key => $value) {
                    update_option($key, $value);
                }
                $saved = true;
            }
        }

        $this->render(
            '@Admin/settings.html.twig',
            [
                'settingsForm' => $settingsForm->createView(),
                'advancedSettingsForm' => $advancedSettingsForm->createView(),
                'saved' => $saved,
            ]
        );
    }

    /**
     * @return FormInterface
     */
    protected function createSettingsForm()
    {
        /** @var FormInterface $form */
        $form = $this->get('form_factory')->create();
        $form->add(
            '_woorefill_api_key',
            null,
            [
                'data' => get_option('_woorefill_api_key'),
                'label' => 'API Key',
                'required' => false,
                'help' => 'API Key to communicate with service to make refills.',
                'attr' => [
                    'style' => 'width:450px',
                ],
            ]
        );


        $form->handleRequest($this->getRequest());

        return $form;
    }

    /**
     * @return FormInterface
     */
    protected function createAdvancedSettingsForm()
    {
        /** @var FormInterface $form */
        $form = $this->get('form_factory')->create();

        $form->add('_woorefill_advanced_settings', 'hidden', ['data' => (boolean) get_option('_woorefill_advanced_settings', 0)]);

        $form->add(
            '_woorefill_prerelease',
            'wp_checkbox',
            [
                'data' => get_option('_woorefill_prerelease') === 'yes',
                'label' => 'Pre-released updates',
                'required' => false,
                'help' => 'Use pre-release updates if you\'re willing to report bugs or any problems that ocurr. <b style="color: orangered">WARNING: Non stable version may contains bugs.<b></b></b>',
            ]
        );

        $form->add(
            '_woorefill_log',
            'wp_checkbox',
            [
                'data' => get_option('_woorefill_log') === 'yes',
                'label' => 'Debug Log',
                'required' => false,
                'help' => sprintf(__('Log WooRefill events, such as API request and responses inside WooCommerce logs')),
            ]
        );

        $form->handleRequest($this->getRequest());

        return $form;
    }
}