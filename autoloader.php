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

include __DIR__.'/vendor/parsedown/Parsedown.php';

require_once __DIR__.'/vendor/twig/twig/lib/Twig/Autoloader.php';
Twig_Autoloader::register();

spl_autoload_register(
    function ($class) {
        $class = str_replace('WooRefill\\', '', $class);
        $file = __DIR__.DIRECTORY_SEPARATOR.'src/'.$class.'.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
);