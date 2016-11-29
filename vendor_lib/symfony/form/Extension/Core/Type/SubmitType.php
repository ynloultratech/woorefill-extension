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
use WooRefill\Symfony\Component\Form\SubmitButtonTypeInterface;

/**
 * A submit button.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class SubmitType extends AbstractType implements SubmitButtonTypeInterface
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['clicked'] = $form->isClicked();
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return __NAMESPACE__.'\ButtonType';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'submit';
    }
}
