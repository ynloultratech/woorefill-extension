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

use WooRefill\Symfony\Component\Form\AbstractType;
use WooRefill\Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use WooRefill\Symfony\Component\Form\FormInterface;
use WooRefill\Symfony\Component\Form\FormView;
use WooRefill\Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ProductTableFormType
 */
class ProductTableFormType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['products'] = $options['products'];
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'products' => [],
                'multiple' => true,
                'expanded' => true,
                'label' => false,
                'data' => [],
            ]
        );

        $resolver->setNormalizer(
            'choices',
            function ($options) {
                $choices = [];
                foreach ($options['products'] as $product) {
                    $choices[$product->id] = $product->name;
                }

                return $choices;
            }
        );

        $resolver->setNormalizer(
            'data',
            function ($options, $value) {
                //select all products by default only if are listing carrier products
                if (count($options['products']) < 20) {
                    foreach ($options['products'] as $product) {
                        $value[] = $product->id;
                    }
                }

                return $value;
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'product_table';
    }

    /**
     * @inheritDoc
     */
    public function getParent()
    {
        return 'choice';
    }
}