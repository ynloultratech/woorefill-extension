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

namespace WooRefill\Admin\Form;

use WooRefill\Admin\Form\Transformer\ImportDataTransformer;
use WooRefill\Admin\Form\Type\CarrierFormType;
use WooRefill\Admin\Form\Type\CategoryFormType;
use WooRefill\Admin\Form\Type\ProductTableFormType;
use WooRefill\Admin\Form\Type\ProductStatusFormType;
use WooRefill\App\Api\WooRefillApi;
use WooRefill\Symfony\Component\Form\AbstractType;
use WooRefill\Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use WooRefill\Symfony\Component\Form\FormBuilderInterface;
use WooRefill\Symfony\Component\Form\FormInterface;
use WooRefill\Symfony\Component\Form\FormView;
use WooRefill\Symfony\Component\HttpFoundation\Request;
use WooRefill\Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ImportForm
 */
class ImportForm extends AbstractType
{
    /**
     * @var WooRefillApi
     */
    protected $api;

    /**
     * @var Request
     */
    protected $request;

    /**
     * ImportForm constructor.
     *
     * @param WooRefillApi $api
     */
    public function __construct(WooRefillApi $api, Request $request)
    {
        $this->api = $api;
        $this->request = $request;
    }

    /**
     * @inheritDoc
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['carrier'] = $options['carrier'];
    }

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new ImportDataTransformer());

        $builder->setAction(admin_url('edit.php?post_type=product&page=import_wireless_products&carrier_id='.$options['carrier_id']));
        $products = $this->api->getProducts($options['carrier_id']);

        $builder->add(
            'products',
            'product_table',
            [
                'products' => $products,
            ]
        );
        $builder->add(
            'createCarrier',
            'choice',
            [
                'wrapper_class' => 'form-field horizontal',
                'choice_attr' => function ($val, $key, $index) {
                    return ['data-toggle-prefix' => '.create-carrier-'];
                },
                'choices' => [
                    1 => 'Create new carrier',
                    0 => 'Use existent carrier',
                ],
                'expanded' => true,
                'help' => __('Import selected products into existent carrier or create new one?'),
                'label' => false,
                'data' => 1,
            ]
        );
        $builder->add(
            'newCarrierName',
            null,
            [
                'wrapper_class' => 'form-field create-carrier-1',
                'data' => $options['carrier'] ? $options['carrier']->name : null,
            ]
        );
        $builder->add(
            'existentCarrier',
            'carrier_choice',
            [
                'wrapper_class' => 'form-field create-carrier-0',
            ]
        );

        $builder->add(
            'createCategory',
            'choice',
            [
                'wrapper_class' => 'form-field horizontal create-carrier-1',
                'choices' => [
                    1 => 'Create new category',
                    0 => 'Use existent category',
                ],
                'choice_attr' => function ($val, $key, $index) {
                    return ['data-toggle-prefix' => '.create-category-'];
                },
                'expanded' => true,
                'help' => __('Create the carrier into existent category or create new one?'),
                'label' => false,
                'data' => 0,
            ]
        );
        $builder->add(
            'newCategoryName',
            null,
            [
                'wrapper_class' => 'form-field horizontal  create-category-1',
                'help' => __('Create new category for this carrier, e.g. Refills or Long Distance'),
            ]
        );
        $builder->add(
            'existentCategory',
            'category_choice',
            [
                'help' => __('Select the category to use for this carrier.'),
                'wrapper_class' => 'form-field horizontal  create-category-0',
            ]
        );
        $builder->add(
            'newCategoryParent',
            'category_choice',
            [
                'wrapper_class' => 'form-field horizontal create-category-1',
                'required' => false,
            ]
        );
        $builder->add(
            'status',
            'product_status_choice',
            [
                'data' => 'publish',
                'help' => __('Status to set for each product after import'),
            ]
        );

        return $builder->getForm();
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'carrier_id' => null,
                'carrier' => null,
                'data_class' => 'WooRefill\Admin\Import\ImportData',
                'wrapper_class' => 'form-wrap',
            ]
        );

        //convert the given carrier_id in a carrier fom api
        $resolver->setNormalizer(
            'carrier_id',
            function ($options, $value) {
                if (!$value) {
                    return $this->request->get('carrier_id');
                }
            }
        );

        //convert the given carrier_id in a carrier fom api
        $resolver->setNormalizer(
            'carrier',
            function ($options, $value) {
                if (!$value && $options['carrier_id']) {
                    return $this->api->getCarrier($options['carrier_id']);
                }
            }
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'import_products_form';
    }
}