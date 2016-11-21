<?php

/*
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 *
 * @author YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package woorefill-extension
 * @version 1.0-alpha
 */

add_action(
    'woocommerce_order_details_after_customer_details',
    function ($order) {
        /** @var WC_Order $order */
        $pin = get_post_meta($order->id, WR_RESPONSE_PREFIX.'PIN', true);
        if ($pin) {
            echo <<<HTML
<tr>
    <th>Pin</th>
    <td>$pin</td>
</tr>
HTML;
        }

    }
);