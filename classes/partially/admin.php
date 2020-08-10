<?php
if( ! defined('ABSPATH') ) die('Not Allowed');

class Partially_Admin {
  function __construct() {
    add_action( 'add_meta_boxes', array($this, 'metaBoxAdd'));
		add_action( 'woocommerce_process_product_meta', array($this, 'saveMeta'), 10, 2 );
  }

	public function metaBoxAdd() {
		add_meta_box( 'partially-product-config', __( 'Partially Options', 'woo-partially' ), array($this, 'productOptions'), 'product', 'side', 'low' );
	}

	public function productOptions() {
		echo '<div class="options_group">';

		woocommerce_wp_checkbox( array(
			'id'      => 'partially_disabled',
			'value'   => get_post_meta( get_the_ID(), 'partially_disabled', true ),
			'label'   => __('Disable for Partially', 'woo-partially'),
			'desc_tip' => true,
			'description' => __('If you select this, Partially will not be available if this product is in the cart', 'woo-partially'),
		) );

		$offerOptions = array('' => __('default offer', 'woo-partially'));

		$gateway = WC_Gateway_Partially::instance();
		foreach ($gateway->get_offers() as $offer) {
			$offerOptions[$offer->id] = $offer->name;
		}

		woocommerce_wp_select(array(
			'id' => 'partially_offer',
			'label' => __('Partially offer', 'woo-partially'),
			'value' => get_post_meta( get_the_ID(), 'partially_offer', true ),
			'options' => $offerOptions
		));

		echo '</div>';
	}

	public function saveMeta($id, $post) {
		update_post_meta($id, 'partially_offer', $_POST['partially_offer']);
		if ( ! empty($_POST['partially_disabled'])) {
			update_post_meta($id, 'partially_disabled', $_POST['partially_disabled']);
		}
		else {
			delete_post_meta($id, 'partially_disabled');
		}
	}
}
