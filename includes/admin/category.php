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
    'product_cat_add_form_fields',
    function () {
        echo <<<HTML
<div class="form-field">
     <label style="display: inline-block">
        <input name="wireless_carrier" type="checkbox" value="1">
        Wireless Carrier
    </label>
    <p>Check if this category represents a wireless carrier. e.g. "Simple Mobile", "AT&T"</p>
</div>
HTML;
    }
);

add_action(
    'product_cat_edit_form_fields',
    function ($term) {
        $value = get_term_meta($term->term_id, 'wireless_carrier', true);
        $checked = $value ? 'checked' : '';
        echo <<<HTML
<tr class="form-field">
			<th scope="row" valign="top"><label>Wireless Carrier</label></th>
			<td>
			<input name="wireless_carrier" type="hidden" value="0">
			<input name="wireless_carrier" type="checkbox" $checked>
        Wireless Carrier
        <p>Check if this category represents a wireless carrier. e.g. "Simple Mobile", "AT&T"</p>
            </td>
</tr>
HTML;

    }
);

add_action('edit_term', 'wr_save_product_category_term', 10, 3);
add_action('create_term', 'wr_save_product_category_term', 10, 3);

function wr_save_product_category_term($term_id, $tt_id = '', $taxonomy = '')
{
    if (isset($_POST['wireless_carrier']) && 'product_cat' === $taxonomy) {
        update_term_meta($term_id, 'wireless_carrier', (boolean)$_POST['wireless_carrier']);
    }
}


