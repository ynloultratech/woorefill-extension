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
    'woocommerce_admin_order_data_after_order_details',
    function () {
        $metaArray = get_post_meta(get_the_ID(), null, true);
        $details = '';
        foreach ($metaArray as $meta => $value) {
            if (strpos($meta, WR_INPUT_META_PREFIX) !== false) {
                $name = wr_humanize_str(str_replace(WR_INPUT_META_PREFIX, '', $meta));
                $value = current($value);
                if ($value) {
                    $details .= "<p><strong>$name:</strong> $value</p>";
                }
            }
            if (strpos($meta, WR_RESPONSE_PREFIX) !== false) {
                $name = wr_humanize_str(str_replace(WR_RESPONSE_PREFIX, '', $meta));
                $value = current($value);
                if ($value) {
                    $details .= "<p><strong>$name:</strong> $value</p>";
                }
            }
        }
        if ($details) {
            echo <<<HTML
<div class="order_data_column_container" style="padding-top: 20px">
<hr>
        <h3>
            Refill Details
        </h3>
        $details
</div>
HTML;
        }

    }
);