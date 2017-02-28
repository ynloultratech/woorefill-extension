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

namespace WooRefill\App\Form;

use WooRefill\App\Form\Extension\WPFormTypeExtension;
use WooRefill\App\Kernel;
use WooRefill\App\TaggedServices\TaggedServices;
use WooRefill\Symfony\Component\DependencyInjection\Container;
use WooRefill\Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use WooRefill\Symfony\Component\Form\Forms;

/**
 * Class FormFactory
 */
class FormFactory
{

    /**
     * createFormFactory
     *
     * @return \WooRefill\Symfony\Component\Form\FormFactoryInterface
     */
    public static function createFormFactory(Container $container)
    {
        $formFactory = Forms::createFormFactoryBuilder();
        $formFactory->addExtension(new HttpFoundationExtension())->getFormFactory();

        /** @var TaggedServices $taggedServices */
        $taggedServices = $container->get('tagged_services');

        //extensions
        $definitions = $taggedServices->findTaggedServices('form.type_extension');
        foreach ($definitions as $definition) {
            $formFactory->addTypeExtension($definition->getService());
        }

        //form types
        $definitions = $taggedServices->findTaggedServices('form.type');
        foreach ($definitions as $definition) {
            $formFactory->addType($definition->getService());
        }

        return $formFactory->getFormFactory();
    }
}