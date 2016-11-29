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
use WooRefill\Symfony\Component\Form\FormBuilderInterface;
use WooRefill\Symfony\Component\Form\FormInterface;
use WooRefill\Symfony\Component\Form\Extension\Core\DataTransformer\BooleanToStringTransformer;
use WooRefill\Symfony\Component\Form\FormView;
use WooRefill\Symfony\Component\OptionsResolver\OptionsResolver;

class CheckboxType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Unlike in other types, where the data is NULL by default, it
        // needs to be a Boolean here. setData(null) is not acceptable
        // for checkboxes and radio buttons (unless a custom model
        // transformer handles this case).
        // We cannot solve this case via overriding the "data" option, because
        // doing so also calls setDataLocked(true).
        $builder->setData(isset($options['data']) ? $options['data'] : false);
        $builder->addViewTransformer(new BooleanToStringTransformer($options['value']));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, array(
            'value' => $options['value'],
            'checked' => null !== $form->getViewData(),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $emptyData = function (FormInterface $form, $viewData) {
            return $viewData;
        };

        $resolver->setDefaults(array(
            'value' => '1',
            'empty_data' => $emptyData,
            'compound' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'checkbox';
    }
}
