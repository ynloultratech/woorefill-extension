<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WooRefill\Symfony\Component\Form\Extension\HttpFoundation\Type;

use WooRefill\Symfony\Component\Form\AbstractTypeExtension;
use WooRefill\Symfony\Component\Form\RequestHandlerInterface;
use WooRefill\Symfony\Component\Form\FormBuilderInterface;
use WooRefill\Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationRequestHandler;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class FormTypeHttpFoundationExtension extends AbstractTypeExtension
{
    /**
     * @var RequestHandlerInterface
     */
    private $requestHandler;

    /**
     * @param RequestHandlerInterface $requestHandler
     */
    public function __construct(RequestHandlerInterface $requestHandler = null)
    {
        $this->requestHandler = $requestHandler ?: new HttpFoundationRequestHandler();
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setRequestHandler($this->requestHandler);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'WooRefill\Symfony\Component\Form\Extension\Core\Type\FormType';
    }
}
