<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



class WC_Shipping_FrontierCo_Store_Pickup extends WC_Shipping_Method {


	public function __construct( $instance_id = 0 ) {
		$this->id                 = 'frontierco_store_pickup';
		$this->instance_id        = absint( $instance_id );
		$this->method_title       = __( 'FrontierCo Store Pickup', 'frontierco-functionality' );
		$this->method_description = __( 'Allows use of the FrontierCo Store Pickup System', 'frontierco-functionality' );
		$this->supports           = array(
			'shipping-zones',
			'instance-settings',
			'instance-settings-modal',
		);

		$this->init();
	}

	public function init() {
		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables.
		$this->title            = $this->get_option( 'title' );

		// Actions.
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
		
	}

	public function init_form_fields() {
		$this->instance_form_fields = array(
			'title'            => array(
				'title'       => __( 'Title', 'frontierco-functionality' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'frontierco-functionality' ),
				'default'     => $this->method_title,
				'desc_tip'    => true,
			)
			
		);
	}

	public function get_instance_form_fields() {
		return parent::get_instance_form_fields();
	}

	public function is_available( $package ) {
		if(!is_admin()):
			$is_available = false;

			$_PICKUPS = get_posts(array('post_type' => 'storepickup', 'posts_per_page' => '-1'));

			if(get_option('frontierco_functionality_enable_store_pickup') && count($_PICKUPS) > 0):
				$is_available = true;
			endif;

			return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', $is_available, $package, $this );

		else:

			return true;

		endif;
	}


	public function calculate_shipping( $package = array() ) {
		$this->add_rate(
			array(
				'label'   => $this->title,
				'cost'    => 0,
				'taxes'   => false,
				'package' => $package,
			)
		);
	}
}
