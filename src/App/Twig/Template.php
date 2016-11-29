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
namespace WooRefill\App\Twig;

use WooRefill\App\Twig\Extension\WPHelperExtension;
use WooRefill\Symfony\Bridge\Twig\AppVariable;
use WooRefill\Symfony\Bridge\Twig\Extension\FormExtension;
use WooRefill\Symfony\Bridge\Twig\Form\TwigRenderer;
use WooRefill\Symfony\Bridge\Twig\Form\TwigRendererEngine;
use WooRefill\Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Template
 */
class Template
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * Template constructor.
     */
    public function __construct(ContainerInterface $container)
    {
        $appVariableReflection = new \ReflectionClass(AppVariable::class);
        $vendorTwigBridgeDir = dirname($appVariableReflection->getFileName());

        $vendorDir = realpath(__DIR__.'/../../../vendor');
        $loader = new \Twig_Loader_Filesystem(
            [
                $vendorDir,
                $vendorTwigBridgeDir.'/Resources/views/Form',
            ]
        );
        $loader->addPath(__DIR__.'/../Resources/views/', 'App');
        $loader->addPath(__DIR__.'/../../Admin/Resources/views/', 'Admin');
        $loader->addPath(__DIR__.'/../../Shop/Resources/views/', 'Shop');
        $this->twig = new \Twig_Environment(
            $loader, [
                'debug' => WOOREFILL_DEBUG,
                'strict_variables' => WOOREFILL_DEBUG,
                'autoescape' => false,
            ]
        );

        $defaultFormThemes = ['@App/form_theme.html.twig'];
        $formEngine = new TwigRendererEngine($defaultFormThemes);
        $formEngine->setEnvironment($this->twig);

        // add the FormExtension to Twig
        $this->twig->addExtension(new FormExtension(new TwigRenderer($formEngine)));

        $this->twig->addExtension(new WPHelperExtension());
        $this->twig->addGlobal('request', $container->get('request'));
    }

    /**
     * render
     *
     * @param string $view
     * @param array  $args
     *
     * @return string
     */
    public function render($view, $args = [])
    {
        echo $this->twig->render($view, $args);
    }
}