<?php
/*
Plugin Name: WooCommerce Minimum and Maximum Order Price
Description: This plugin adds a minimum and maximum order price requirement for WooCommerce checkout. Settings for this plugin can be edited under WooCommerce > Settings > General.
Version: 1.0
Author: myok
*/

// Add settings to WooCommerce
function wc_min_max_order_price_settings($settings) {
    $new_settings = array(
        array(
            'title' => 'Min & Max Order Price',
            'type' => 'title',
            'desc' => '',
            'id' => 'min_max_order_price'
        ),
        array(
            'title' => 'Minimum Order Price',
            'desc' => 'Enter the minimum allowed order price.',
            'id' => 'wc_min_order_price',
            'type' => 'number',
            'css' => 'min-width:300px;',
            'default' => '30'
        ),
        array(
            'title' => 'Maximum Order Price',
            'desc' => 'Enter the maximum allowed order price.',
            'id' => 'wc_max_order_price',
            'type' => 'number',
            'css' => 'min-width:300px;',
            'default' => '50'
        ),
        array(
            'title' => 'Checkout Notice',
            'desc' => 'This message will display when the order price doesn\'t meet the requirements.',
            'id' => 'wc_min_max_order_notice',
            'type' => 'textarea',
            'css' => 'min-width:300px;',
            'default' => '<strong>The minimum order is $30 and the max is $50! You can edit this message and limits under WooCommerce > Settings > General.</strong>'
        ),
        array(
            'type' => 'sectionend',
            'id' => 'min_max_order_price'
        )
    );

    return array_merge($settings, $new_settings);
}
add_filter('woocommerce_general_settings', 'wc_min_max_order_price_settings');

// Check the order price during checkout
function wc_min_max_order_price_check() {
    if (is_checkout()) {
        $min_price = floatval(get_option('wc_min_order_price', 0));
        $max_price = floatval(get_option('wc_max_order_price', PHP_INT_MAX));
        $notice = get_option('wc_min_max_order_notice');
        
        $cart_total = WC()->cart->total;
        
        if ($cart_total < $min_price || ($max_price && $cart_total > $max_price)) {
            wc_print_notice($notice, 'error');
            remove_action('woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20);
        }
    }
}
add_action('woocommerce_before_checkout_form', 'wc_min_max_order_price_check');
