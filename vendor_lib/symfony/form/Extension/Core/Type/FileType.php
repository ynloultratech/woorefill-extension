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

class FileType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['multiple']) {
            $view->vars['full_name'] .= '[]';
            $view->vars['attr']['multiple'] = 'multiple';
        }

        $view->vars = array_replace($view->vars, array(
            'type' => 'file',
            'value' => '',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view
            ->vars['multipart'] = true
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'compound' => false,
            'data_class' => 'WooRefill\Symfony\Component\HttpFoundation\File\File',
            'empty_data' => null,
            'multiple' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'file';
    }
}
