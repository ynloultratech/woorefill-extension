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

namespace WooRefill\Admin\Page;

use WooRefill\App\DependencyInjection\CommonServiceTrait;
use WooRefill\Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Class Category
 */
class Category implements ContainerAwareInterface
{
    use CommonServiceTrait;

    public function categoryAddFormFields()
    {
        $this->render('@Admin/category/category_add_form_fields.html.twig');
    }

    public function categoryEditFormFields($term)
    {
        $value = (boolean)get_term_meta($term->term_id, 'wireless_carrier', true);
        $this->render('@Admin/category/category_edit_form_fields.html.twig', ['checked' => $value]);
    }

    public function saveCategory($term_id, $tt_id = '', $taxonomy = '')
    {
        if (isset($_POST['wireless_carrier']) && 'product_cat' === $taxonomy) {
            update_term_meta($term_id, 'wireless_carrier', (boolean)$_POST['wireless_carrier']);
        }
    }
}