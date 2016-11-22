<?php

/*
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 *
 * @author YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package woorefill-extension
 * @version 1.0.x
 */

add_action(
    'woocommerce_thankyou',
    function ($id) {

        //prepend PIN before other messages
        $pin = get_post_meta($id, WR_RESPONSE_PREFIX.'PIN', true);

        if ($pin) {
            echo <<<HTML
<div style="display: none">
    <li id="wr-response-pin" class="pin">
        Pin:
        <h3><strong>$pin</strong></h3>
    </li>
</div>
        <script>
jQuery('.woocommerce-thankyou-order-details').prepend(jQuery('#wr-response-pin'));
</script>
HTML;
        }


    }
);