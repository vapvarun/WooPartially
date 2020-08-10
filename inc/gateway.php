<?php
if( ! defined('ABSPATH') ) die('Not Allowed');

add_filter('woocommerce_payment_gateways', 'add_partially_gateway');

add_filter('woocommerce_available_payment_gateways', 'maybe_remove_partially');

function add_partially_gateway($methods) {
    $methods[] = 'WC_Gateway_Partially';
    return $methods;
}

function maybe_remove_partially($gateways) {
  // no cart to check in admin
  if (is_admin()) return $gateways;
  
  $hasDisabled = false;

  // check for products in the cart with Partially disabled
  foreach (WC()->cart->get_cart() as $cart_item_key => $item) {
    if (get_post_meta( $item['product_id'], 'partially_disabled', true ) == 'yes') {
      $hasDisabled = true;
    }
  }

  // check for a global Partially min
  $cartTotal = WC()->cart->get_total(null);
  $gateway = WC_Gateway_Partially::instance();
  $globalMin = $gateway->get_option('min_amount');
  if ( ! empty($globalMin) && $globalMin > 0 && $cartTotal < $globalMin) $hasDisabled = true;

  // check for a global Partially max
  $globalMax = $gateway->get_option('max_amount');
  if ( ! empty($globalMax) && $globalMax > 0 && $cartTotal > $globalMax) $hasDisabled = true;

  if ($hasDisabled) unset($gateways['partially']);

  return $gateways;
}
