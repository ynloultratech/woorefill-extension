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
use WooRefillSymfony\Component\DependencyInjection\ContainerAwareInterface;
use WooRefillSymfony\Component\DependencyInjection\ContainerAwareTrait;
use WooRefillSymfony\Component\Form\Extension\Core\Type\FormType;
use WooRefillSymfony\Component\Form\FormFactory;
use WooRefillSymfony\Component\HttpFoundation\JsonResponse;
use WooRefillSymfony\Component\HttpFoundation\Request;
use WooRefill\App\Twig\Template;
use WooRefillSymfony\Component\Form\FormBuilderInterface;

/**
 * Class Controller
 */
abstract class Controller implements ContainerAwareInterface
{
    use CommonServiceTrait;

    /**
     * @return FormBuilderInterface
     */
    protected function createFormBuilder($type = 'WooRefillSymfony\Component\Form\Extension\Core\Type\FormType', $data = null, array $options = [])
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