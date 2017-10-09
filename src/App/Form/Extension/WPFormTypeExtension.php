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

namespace WooRefill\App\Form\Extension;

use WooRefillSymfony\Component\Form\AbstractTypeExtension;
use WooRefillSymfony\Component\Form\Extension\Core\Type\FormType;
use WooRefillSymfony\Component\Form\FormInterface;
use WooRefillSymfony\Component\Form\FormView;
use WooRefillSymfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class WPFormTypeExtension
 */
class WPFormTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['help'] = $options['help'];
        $view->vars['wrapper_class'] = $options['wrapper_class'];

        if (isset($options['expanded']) && $options['expanded']) {
            $view->vars['label_attr'] = [
                'style' => 'display: inline-block; margin-right: 10px;',
            ];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'help' => '',
                'wrapper_class' => 'form-field',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'form';
    }
}