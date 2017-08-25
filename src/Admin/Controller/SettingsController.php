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
                'help' => sprintf(__('Log WooRefill events, such as API request and responses, inside <code>%s</code>'), wc_get_log_file_path('woorefill')),
            ]
        );

        $form->handleRequest($this->getRequest());
        $saved = false;
        if ($form->isSubmitted()) {
            $givenApiKey = $form->get('_woorefill_api_key')->getData();

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
                        $form->addError(new FormError('Your API key is not valid, please verify or get a new API key.'));
                    }
                }
            }

            if ($form->isValid()) {
                $data = $form->getData();
                foreach ($data as $key => $value) {
                    update_option($key, $value);
                }
                $saved = true;
            }
        }

        $this->render(
            '@Admin/settings.html.twig',
            [
                'form' => $form->createView(),
                'saved' => $saved,
            ]
        );
    }
}