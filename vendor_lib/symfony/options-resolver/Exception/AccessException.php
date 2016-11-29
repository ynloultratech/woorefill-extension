<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WooRefill\Symfony\Component\OptionsResolver\Exception;

/**
 * Thrown when trying to read an option outside of or write it inside of
 * {@link \WooRefill\Symfony\Component\OptionsResolver\Options::resolve()}.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class AccessException extends \LogicException implements ExceptionInterface
{
}
