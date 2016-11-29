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

use WooRefill\App\EntityManager\ProductCategoryManager;
use WooRefill\Symfony\Component\Form\AbstractType;
use WooRefill\Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use WooRefill\Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CategoryFormType
 */
class CategoryFormType extends AbstractType
{
    protected $manager;

    /**
     * CategoryFormType constructor.
     */
    public function __construct(ProductCategoryManager $categoryManager)
    {
        $this->manager = $categoryManager;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $categoriesArray = $this->manager->getProductsCategories();
        $categories = [];
        foreach ($categoriesArray as $category) {
            $categories[$category->term_id] = sprintf('%s (%s)', $category->name, $category->count);
        }
        $resolver->setDefaults(
            [
                'placeholder' => '(Select a Category)',
                'choices' => $categories,
            ]
        );
    }

    /**
     *{@inheritDoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'category_choice';
    }
}