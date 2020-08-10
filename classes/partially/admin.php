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

		woocommerce_wp_multiple_select(array(
			'id' => 'partially_offer',
			'label' => __('Partially offer', 'woo-partially'),
			'value' => get_post_meta( get_the_ID(), 'partially_offer', true ),
			'options' => $offerOptions,
			'custom_attributes'	=> array('multiple' => 'multiple' )
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

/**
 * Output a select input box.
 *
 * @param array $field Data about the field to render.
 */
function woocommerce_wp_multiple_select( $field ) {
	global $thepostid, $post;

	$thepostid = empty( $thepostid ) ? $post->ID : $thepostid;
	$field     = wp_parse_args(
		$field, array(
			'class'             => 'select short',
			'style'             => '',
			'wrapper_class'     => '',
			'value'             => get_post_meta( $thepostid, $field['id'], true ),
			'name'              => $field['id'],
			'desc_tip'          => false,
			'custom_attributes' => array(),
		)
	);

	$wrapper_attributes = array(
		'class' => $field['wrapper_class'] . " form-field {$field['id']}_field",
	);

	$label_attributes = array(
		'for' => $field['id'],
	);

	$field_attributes          = (array) $field['custom_attributes'];
	$field_attributes['style'] = $field['style'];
	$field_attributes['id']    = $field['id'];
	$field_attributes['name']  = $field['name'].'[]';
	$field_attributes['class'] = $field['class'];

	$tooltip     = ! empty( $field['description'] ) && false !== $field['desc_tip'] ? $field['description'] : '';
	$description = ! empty( $field['description'] ) && false === $field['desc_tip'] ? $field['description'] : '';
	?>
	<p <?php echo wc_implode_html_attributes( $wrapper_attributes ); // WPCS: XSS ok. ?>>
		<label <?php echo wc_implode_html_attributes( $label_attributes ); // WPCS: XSS ok. ?>><?php echo wp_kses_post( $field['label'] ); ?></label>
		<?php if ( $tooltip ) : ?>
			<?php echo wc_help_tip( $tooltip ); // WPCS: XSS ok. ?>
		<?php endif; ?>
		<select <?php echo wc_implode_html_attributes( $field_attributes ); // WPCS: XSS ok. ?>>
			<?php
			foreach ( $field['options'] as $key => $value ) {
				echo '<option value="' . esc_attr( $key ) . '"' . wc_selected( $key, $field['value'] ) . '>' . esc_html( $value ) . '</option>';
			}
			?>
		</select>
		<?php if ( $description ) : ?>
			<span class="description"><?php echo wp_kses_post( $description ); ?></span>
		<?php endif; ?>
	</p>
	<?php
}
