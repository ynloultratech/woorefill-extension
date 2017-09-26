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

namespace WooRefill\App\Exception;

use WooRefill\App\Model\ValidationError;

class ValidationException extends \Exception
{
    /**
     * @var ValidationError[]
     */
    protected $errors = [];

    /**
     * ValidationException constructor.
     *
     * @param string            $message
     * @param ValidationError[] $errors
     * @param int               $code
     */
    public function __construct($message, array $errors, $code = 0)
    {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    /**
     * @return ValidationError[]
     */
    public function getErrors()
    {
        return $this->errors;
    }
}