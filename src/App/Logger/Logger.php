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
     *
     * @deprecated
     */
    public function addErrorLog($message)
    {
        $this->addLog('ERROR: '.$message, array_slice(func_get_args(), 1));
    }

    /**
     * @param string $message
     * @param array  $params
     */
    public function warning($message, $params = [])
    {
        $this->log(\WC_Log_Levels::WARNING, $message, $params);
    }

    /**
     * @param string $message
     * @param array  $params
     */
    public function info($message, $params = [])
    {
        $this->log(\WC_Log_Levels::INFO, $message, $params);
    }

    /**
     * @param string $message
     * @param array  $params
     */
    public function error($message, $params = [])
    {
        $this->log(\WC_Log_Levels::ERROR, $message, $params);
    }

    /**
     * @param string $level
     * @param string $message
     * @param array  $params
     */
    public function log($level, $message, $params = [])
    {
        if ($this->logsEnabled) {
            if (is_array($message) || is_object($message)) {
                $message = print_r($message, true);
            }

            if ($params) {
                $message = vsprintf($message, $params);
            }

            wc_get_logger()->log($level, 'WooRefill: '.$message);
        }
    }

    /**
     * addLog
     *
     * @param       $message
     *
     * @deprecated
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