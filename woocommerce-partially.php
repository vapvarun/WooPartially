<?php
/**
* Plugin Name: Woo Partial.ly
* License: GPLv2 or later
* Plugin URI: https://partial.ly
* Version: 52.1.7
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



add_action('woo_after_payment_method', 'partially_woo_after_payment_method');
function partially_woo_after_payment_method( $gateway ){
	if ( $gateway->id == 'partially' ) {	
		$defaultOfferId = $gateway->get_option('offer-id');
		foreach ($gateway->get_offers() as $offer) {
			$offerOptions[$offer->id] = $offer->name;
		}
		
		
		// see if any products override the offer
		foreach (WC()->cart->get_cart() as $cart_item_key => $item) {
			$customOffer = get_post_meta( $item['product_id'], 'partially_offer', true );
		}
		?>
		<div class="payment_box payment_method_<?php echo esc_attr( $gateway->id ); ?>" <?php if ( ! $gateway->chosen ) : /* phpcs:ignore Squiz.ControlStructures.ControlSignature.NewlineAfterOpenBrace */ ?>style="display:none;"<?php endif; /* phpcs:ignore Squiz.ControlStructures.ControlSignature.NewlineAfterOpenBrace */ ?>>
			<select name="woo-partially-offer" >
				<?php foreach( $offerOptions as $key=>$value):?>
					<?php if ( in_array($key , $customOffer)):?>
						<option value="<?php echo $key;?>"><?php echo $value;?></option>
					<?php endif;?>
				<?php endforeach;?>
			</select>
		</div>
		<?php
		
	}
}