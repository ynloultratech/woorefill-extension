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

namespace WooRefill\Admin\Form\Type;

use WooRefill\Admin\Form\Transformer\WPCheckboxValueTransformer;
use WooRefillSymfony\Component\Form\AbstractType;
use WooRefillSymfony\Component\Form\FormBuilderInterface;

/**
 * Class CarrierFormType
 */
class WPCheckboxType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new WPCheckboxValueTransformer($options['value']));
        $builder->addModelTransformer(new WPCheckboxValueTransformer($options['value']));
    }

    /**
     * @inheritDoc
     */
    public function getParent()
    {
       return 'checkbox';
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'wp_checkbox';
    }
}