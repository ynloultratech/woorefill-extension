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
use WooRefill\Symfony\Component\Form\FormInterface;
use WooRefill\Symfony\Component\Form\FormView;
use WooRefill\Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['always_empty'] || !$form->isSubmitted()) {
            $view->vars['value'] = '';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'always_empty' => true,
            'trim' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return __NAMESPACE__.'\TextType';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'password';
    }
}
