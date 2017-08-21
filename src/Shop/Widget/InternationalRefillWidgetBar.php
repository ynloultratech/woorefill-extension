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

namespace WooRefill\Shop\Widget;

use WooRefill\App\Asset\AssetRegister;
use WooRefill\App\DependencyInjection\CommonServiceTrait;
use WooRefill\App\Kernel;
use WooRefillSymfony\Component\DependencyInjection\ContainerAwareInterface;

class InternationalRefillWidgetBar extends \WP_Widget implements ContainerAwareInterface
{
    use CommonServiceTrait;

    function __construct()
    {
        //wp instance this widget by itself
        $this->container = Kernel::getContainer();

        parent::__construct(
            'woorefill_int_refill_widget_bar',

            'WooRefill QuickRefill',

            // Widget description
            ['description' => __('Phone input to allow start a refill process in any place.', 'wpb_widget_domain'),]
        );
    }

    public function registerWidget()
    {
        register_widget('WooRefill\Shop\Widget\InternationalRefillWidgetBar');
    }

    public function widget($args, $instance)
    {
        global $wpdb;

        /** @var AssetRegister $register */
        $register = $this->container->get('asset_register');
        $register->enqueueStyle('intlTelInput', '/public/int-tel-input/css/intlTelInput.css');
        $register->enqueueScript('intlTelInputUtils', '/public/int-tel-input/js/utils.js');
        $register->enqueueScript('intlTelInput', '/public/int-tel-input/js/intlTelInput.js');
        $register->enqueueScript('inputmask', '/public/js/jquery.inputmask.bundle.js');

        $defaultCountry = get_option('woocommerce_default_country', 'US');
        if (strpos($defaultCountry, ':') !== false) {
            list($defaultCountry,) = explode(':', $defaultCountry);
        }

        //rea the list of possible countries
        $sql = "SELECT DISTINCT meta_value 
FROM {$wpdb->postmeta} 
LEFT JOIN {$wpdb->posts} ON post_id =  {$wpdb->posts}.id
WHERE {$wpdb->postmeta}.meta_key = '_wireless_country_code' 
AND {$wpdb->posts}.post_status = 'publish'";

        $sql_result = $wpdb->get_results($sql, ARRAY_A);
        $countries = [];
        foreach ($sql_result as $result) {
            $countries[] = $result['meta_value'];
        }

        if (empty($countries)) {
            $countries[] = $defaultCountry;
        }

        if (!in_array($defaultCountry, $countries)) {
            $defaultCountry = $countries[0];
        }

        $countries = array_unique($countries);

        $country = $this->getRequest()->get('country', $defaultCountry);
        $id = md5(mt_rand());

        $title = apply_filters('widget_title', $instance['title']);

        $this->render(
            '@Shop/widget/int_refill_bar.html.twig',
            [
                'id' => $id,
                'country' => $country,
                'title' => $title,
                'before_widget' => $args['before_widget'],
                'before_title' => $args['before_title'],
                'after_title' => $args['after_title'],
                'after_widget' => $args['after_widget'],
                'ok_btn' => $instance['ok_btn'] ?: 'Go',
                'defaultCountry' => $defaultCountry,
                'phone' => $this->getRequest()->get('phone'),
                'dial_code' => $this->getRequest()->get('dial_code'),
                'countries' => $countries,
                'allowDropdown' => count($countries) > 1,
            ]
        );
    }

    // Widget Backend
    public function form($instance)
    {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = '';
        }

        if (isset($instance['ok_btn'])) {
            $okBtn = $instance['ok_btn'];
        } else {
            $okBtn = 'Go';
        }

        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>"/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('ok_btn'); ?>"><?php _e('Submit button label:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('ok_btn'); ?>" name="<?php echo $this->get_field_name('ok_btn'); ?>" type="text" value="<?php echo esc_attr($okBtn); ?>"/>
        </p>
        <?php
    }
}