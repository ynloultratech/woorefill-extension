<?php

/**
 * TotalReup (r) Payment Center (https://www.totalreup.com)
 *
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @author    Total Developer Team <developer@totalreup.com>
 * @copyright 2015 Copyright(c) Total Mobile (https://totalmobile.com) - All rights reserved.
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