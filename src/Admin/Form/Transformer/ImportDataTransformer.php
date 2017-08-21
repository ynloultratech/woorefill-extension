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

use WooRefill\Admin\Import\ImportData;
use WooRefillSymfony\Component\Form\DataTransformerInterface;

/**
 * Class ImportDataTransformer
 */
class ImportDataTransformer implements DataTransformerInterface
{
    /**
     * @inheritDoc
     */
    public function transform($value)
    {
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function reverseTransform($value)
    {
        if ($value instanceof ImportData){
            if ($value->getExistentCarrier()) {
                $value->setExistentCarrier(get_term($value->getExistentCarrier()));
            }
            if ($value->getExistentCategory()) {
                $value->setExistentCategory(get_term($value->getExistentCategory()));
            }
            if ($value->getNewCategoryParent()) {
                $value->setNewCategoryParent(get_term($value->getNewCategoryParent()));
            }
        }
        return $value;
    }
}