<?php
/**
* Plugin Name: Woo Partial.ly
* License: GPLv2 or later
* Plugin URI: https://partial.ly
* Version: 2.1.7
* Description: Add Partial.ly payment plans to your WooCommerce store
* Author: Partially LLC
* Author URI: https://partial.ly
* Requires at least: 4.4
* Tested up to: 5.4
* WC tested up to: 4.0
* WC requires at least: 2.6
*/

if ( ! defined( 'ABSPATH' ) ) exit;

define('PARTIALLY_PATH', untrailingslashit(plugin_dir_path( __FILE__ )) );

// make sure woocommerce exists
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

    add_action('plugins_loaded', 'woocommerce_partially_init');
}


function woocommerce_partially_init() {

    if ( ! class_exists('WC_Payment_Gateway'))  return;

    include_once PARTIALLY_PATH . "/classes/partially/gateway.php";

    include_once PARTIALLY_PATH . "/inc/notification-handler.php";

    if (is_admin()) {

    	include_once PARTIALLY_PATH . "/classes/partially/admin.php";

    	$partiallyAdmin = new Partially_Admin();
    }

    include_once PARTIALLY_PATH . "/inc/gateway.php";

    include_once PARTIALLY_PATH . "/inc/widget.php";

    include_once PARTIALLY_PATH . "/inc/checkout-button.php";

}
