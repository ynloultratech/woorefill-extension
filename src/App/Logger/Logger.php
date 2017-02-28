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

namespace WooRefill\App\Logger;

/**
 * Class Logger
 */
class Logger
{
    protected $logsEnabled = false;

    /**
     * Logger constructor.
     *
     * @param bool $logsEnabled
     */
    public function __construct($logsEnabled)
    {
        $this->logsEnabled = $logsEnabled;
    }

    /**
     * addErrorLog
     *
     * @param $message
     */
    public function addErrorLog($message)
    {
        $this->addLog('ERROR: '.$message, array_slice(func_get_args(), 1));
    }

    /**
     * addLog
     *
     * @param       $message
     */
    public function addLog($message)
    {
        if ($this->logsEnabled) {
            if (is_array($message) || is_object($message)) {
                $message = print_r($message, true);
            }

            $args = array_slice(func_get_args(), 1);
            if (is_string($message) && $args) {
                $message = vsprintf($message, $args);
            }

            $log = new \WC_Logger();
            $log->add('woorefill', $message);
        }
    }
}