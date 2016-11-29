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

namespace WooRefill\App\Asset;

class AssetRegister
{
    /**
     * @param       $name
     * @param       $file
     * @param array $deps
     */
    public function enqueueScript($name, $file, $deps = [])
    {
        wp_enqueue_script($name, $this->getFilePath($file), $deps);
    }

    /**
     * @param       $name
     * @param       $file
     * @param array $deps
     */
    public function enqueueStyle($name, $file, $deps = [])
    {
        wp_enqueue_style($name, $this->getFilePath($file), $deps);
    }

    /**
     * getFilePath
     *
     * @param $file
     *
     * @return string
     */
    protected function getFilePath($file)
    {
        $plugin = __DIR__.'/../../../woorefill.php';
        $plugin = realpath($plugin);
        return plugins_url($file, $plugin);
    }
}