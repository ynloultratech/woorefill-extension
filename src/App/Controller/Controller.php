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

namespace WooRefill\App\Controller;

use WooRefill\App\DependencyInjection\CommonServiceTrait;
use WooRefill\App\Event\EventDispatcherBridge;
use WooRefill\App\Kernel;
use WooRefill\Symfony\Component\DependencyInjection\ContainerAwareInterface;
use WooRefill\Symfony\Component\DependencyInjection\ContainerAwareTrait;
use WooRefill\Symfony\Component\Form\Extension\Core\Type\FormType;
use WooRefill\Symfony\Component\Form\FormFactory;
use WooRefill\Symfony\Component\HttpFoundation\JsonResponse;
use WooRefill\Symfony\Component\HttpFoundation\Request;
use WooRefill\App\Twig\Template;
use WooRefill\Symfony\Component\Form\FormBuilderInterface;

/**
 * Class Controller
 */
abstract class Controller implements ContainerAwareInterface
{
    use CommonServiceTrait;

    /**
     * @return FormBuilderInterface
     */
    protected function createFormBuilder($type = 'WooRefill\Symfony\Component\Form\Extension\Core\Type\FormType', $data = null, array $options = [])
    {
        /** @var FormFactory $formFactory */
        $formFactory = $this->get('form_factory');

        return $formFactory->createBuilder($type, $data, $options);
    }

    /**
     * @param       $view
     * @param array $args
     *
     * @return string
     */
    protected function render($view, $args = [])
    {
        /** @var Template $template */
        $template = $this->get('template');

        echo $template->render($view, $args);
    }

    /**
     * @param       $view
     * @param array $args
     *
     * @return string
     */
    protected function renderAjax($view, $args = [])
    {
        $this->render($view, $args);
        exit;
    }

    /**
     * @param array $data
     */
    protected function renderJson($data)
    {
        //clean wp
        ob_clean();
        JsonResponse::create($data)->send();
        exit;
    }
}