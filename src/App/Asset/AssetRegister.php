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

namespace WooRefill\App\Asset;

class AssetRegister
{
    /**
     * @var string
     */
    protected $pluginFile;

    /**
     * AssetRegister constructor.
     *
     * @param string $pluginFile
     */
    public function __construct($pluginFile)
    {
        $this->pluginFile = $pluginFile;
    }

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
        return plugins_url($file, $this->pluginFile);
    }
}