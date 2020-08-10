<?php
if( ! defined('ABSPATH') ) die('Not Allowed');

add_action('parse_request', 'checkPartiallyNotificationUrl');

function checkPartiallyNotificationUrl() {
  $path = $_SERVER['REQUEST_URI'];
  if ($path == WC_Gateway_Partially::NOTIFICATION_PATH) {
    $gateway = WC_Gateway_Partially::instance();
    $gateway->handlePartiallyNotification();
    exit();
  }
}
