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

namespace WooRefill\Admin\Form\Transformer;

use WooRefillSymfony\Component\Form\DataTransformerInterface;

/**
 * Class ImportDataTransformer
 */
class WPCheckboxValueTransformer implements DataTransformerInterface
{
    /**
     * @inheritDoc
     */
    public function transform($value)
    {
        if (is_string($value)) {
            return $value == 'yes' ? true : false;
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function reverseTransform($value)
    {
        if (is_bool($value)) {
            return $value ? 'yes' : 'no';
        }

        return $value;
    }
}