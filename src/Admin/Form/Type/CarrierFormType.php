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

use WooRefillSymfony\Component\Form\AbstractType;
use WooRefillSymfony\Component\Form\Extension\Core\Type\ChoiceType;
use WooRefillSymfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CarrierFormType
 */
class CarrierFormType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        /** @var \WP_Term[] $carriers */
        $terms = get_terms(
            [
                'taxonomy' => 'product_cat',
                'hide_empty' => false,
                'show_count' => true,
                'meta_key' => 'wireless_carrier',
                'meta_value' => '1',
            ]
        );
        $carriers = [];
        foreach ($terms as $term) {
            $carriers[$term->term_id] = sprintf('%s (%s)', $term->name, $term->count);
        }
        $resolver->setDefaults(
            [
                'placeholder' => '(Select a Carrier)',
                'choices' => $carriers,
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
        return 'carrier_choice';
    }
}