<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WooRefill\Symfony\Component\Form\Extension\Csrf\Type;

use WooRefill\Symfony\Component\Form\AbstractTypeExtension;
use WooRefill\Symfony\Component\Form\Extension\Csrf\EventListener\CsrfValidationListener;
use WooRefill\Symfony\Component\Form\FormBuilderInterface;
use WooRefill\Symfony\Component\Form\FormView;
use WooRefill\Symfony\Component\Form\FormInterface;
use WooRefill\Symfony\Component\Form\Util\ServerParams;
use WooRefill\Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class FormTypeCsrfExtension extends AbstractTypeExtension
{
    /**
     * @var CsrfTokenManagerInterface
     */
    private $defaultTokenManager;

    /**
     * @var bool
     */
    private $defaultEnabled;

    /**
     * @var string
     */
    private $defaultFieldName;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var null|string
     */
    private $translationDomain;

    /**
     * @var ServerParams
     */
    private $serverParams;

    public function __construct(CsrfTokenManagerInterface $defaultTokenManager, $defaultEnabled = true, $defaultFieldName = '_token', TranslatorInterface $translator = null, $translationDomain = null, ServerParams $serverParams = null)
    {
        $this->defaultTokenManager = $defaultTokenManager;
        $this->defaultEnabled = $defaultEnabled;
        $this->defaultFieldName = $defaultFieldName;
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
        $this->serverParams = $serverParams;
    }

    /**
     * Adds a CSRF field to the form when the CSRF protection is enabled.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['csrf_protection']) {
            return;
        }

        $builder
            ->addEventSubscriber(new CsrfValidationListener(
                $options['csrf_field_name'],
                $options['csrf_token_manager'],
                $options['csrf_token_id'] ?: ($builder->getName() ?: get_class($builder->getType()->getInnerType())),
                $options['csrf_message'],
                $this->translator,
                $this->translationDomain,
                $this->serverParams
            ))
        ;
    }

    /**
     * Adds a CSRF field to the root form view.
     *
     * @param FormView      $view    The form view
     * @param FormInterface $form    The form
     * @param array         $options The options
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['csrf_protection'] && !$view->parent && $options['compound']) {
            $factory = $form->getConfig()->getFormFactory();
            $tokenId = $options['csrf_token_id'] ?: ($form->getName() ?: get_class($form->getConfig()->getType()->getInnerType()));
            $data = (string) $options['csrf_token_manager']->getToken($tokenId);

            $csrfForm = $factory->createNamed($options['csrf_field_name'], 'WooRefill\Symfony\Component\Form\Extension\Core\Type\HiddenType', $data, array(
                'mapped' => false,
            ));

            $view->children[$options['csrf_field_name']] = $csrfForm->createView($view);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => $this->defaultEnabled,
            'csrf_field_name' => $this->defaultFieldName,
            'csrf_message' => 'The CSRF token is invalid. Please try to resubmit the form.',
            'csrf_token_manager' => $this->defaultTokenManager,
            'csrf_token_id' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'WooRefill\Symfony\Component\Form\Extension\Core\Type\FormType';
    }
}
