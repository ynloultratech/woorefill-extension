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

namespace WooRefill\Admin\Form\Type;

use WooRefill\Symfony\Component\Form\AbstractType;
use WooRefill\Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use WooRefill\Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ProductStatusFormType
 */
class ProductStatusFormType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'choices' => [
                    'pending' => 'Pending',
                    'draft' => 'Draft',
                    'publish' => 'Publish',
                ],
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'product_status_choice';
    }
}