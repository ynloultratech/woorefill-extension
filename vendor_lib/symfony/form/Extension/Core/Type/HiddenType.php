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
use WooRefill\Symfony\Component\OptionsResolver\OptionsResolver;

class HiddenType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            // hidden fields cannot have a required attribute
            'required' => false,
            // Pass errors to the parent
            'error_bubbling' => true,
            'compound' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'hidden';
    }
}
