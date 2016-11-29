<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WooRefill\Symfony\Component\Form\Extension\Core\Type;

use WooRefill\Symfony\Component\Form\AbstractType;

class RadioType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return __NAMESPACE__.'\CheckboxType';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'radio';
    }
}
