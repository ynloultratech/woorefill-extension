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

namespace WooRefill\Shop;

use WooRefill\App\DependencyInjection\CommonServiceTrait;
use WooRefillSymfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Class ThankYou
 */
class ThankYou implements ContainerAwareInterface
{
    use CommonServiceTrait;

    public function thankYou($id)
    {
        //prepend PIN before other messages
        $pin = get_post_meta($id, '_woo_api_response_PIN', true);
        if ($pin) {
            $this->render('@Shop/thankyou/pin.html.twig', ['pin' => $pin]);
        }
    }
}