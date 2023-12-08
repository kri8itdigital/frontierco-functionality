<?php
/**
 * WooCommerce Account Settings.
 *
 * @package WooCommerce/Admin
 */
/**
 * WC_Settings_Accounts.
 */
class Frontierco_Functionality_Settings extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'frontierco-functionality';
		$this->label = __( 'FrontierCo Settings', 'frontierco-functionality' );
		parent::__construct();
	} 


	public function get_sections() {
		$sections = array(
			'' 				=> __( 'Store Pickup Settings', 'frontierco-functionality' ),
		);

		return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
	}

	

	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	public function get_settings($_CURRENT = '') {

		$_SETTINGS = array();

		if(isset($_GET['section'])):
			$_CURRENT = $_GET['section'];
		endif;


		switch($_CURRENT):

			case '':
				
				$_SETTINGS = array(
					array(
						'title' => __( 'FrontierCo Store Pickup Settings', 'frontierco-functionality' ),
						'type'  => 'title',
						'id'    => 'frontierco_settings',
					),
					array(
						'title'         => __( 'Store Pickup', 'frontierco-functionality' ),
						'desc'          => __( 'Enable Store Pickup on Frontend', 'frontierco-functionality' ),
						'id'            => 'frontierco_functionality_enable_store_pickup',
						'default'       => 'no',
						'checkboxgroup' => 'start',
						'type'          => 'checkbox'
					),
					array(
						'title'         => __( 'Store Pickup Selector Label', 'frontierco-functionality' ),
						'desc'          => __( 'The text for the label where the store dropdown is presented at checkout.', 'frontierco-functionality' ),
						'id'            => 'frontierco_functionality_store_pickup_dropdown_label',
						'default'       => 'Please select a store for pickup',
						'type'          => 'text'
					),
					array(
						'title'         => __( 'Store Pickup Selector Placeholder', 'frontierco-functionality' ),
						'desc'          => __( 'The placeholder for the store dropdown at checkout.', 'frontierco-functionality' ),
						'id'            => 'frontierco_functionality_store_pickup_dropdown_placeholder',
						'default'       => 'Please select store',
						'type'          => 'text'
					),
					array(
						'title'         => __( 'Store Pickup Selector Error', 'frontierco-functionality' ),
						'desc'          => __( 'The error message if a store pickup location is not selected.', 'frontierco-functionality' ),
						'id'            => 'frontierco_functionality_store_pickup_dropdown_error',
						'default'       => 'Kindly select a pickup location to continue',
						'type'          => 'text'
					),
					array(
						'type' => 'sectionend',
						'id'   => 'frontierco_settings',
					),
				);
			break;

		endswitch;
		

		$_THE_SETTINGS = apply_filters(
			'woocommerce_' . $this->id . '_settings',
			$_SETTINGS
		);

		return apply_filters( 'woocommerce_get_settings_' . $this->id, $_THE_SETTINGS );
	}

	

	/**
	 * Save settings.
	 *
	 * @return array
	 */
	public function save(){

		$_SETTINGS = $this->get_settings();

		if(isset($_GET['section'])):
			$_SECTION = $_GET['section'];
		else:
			$_SECTION = '';
		endif;


		switch($_SECTION):

			case "":
				

			break;

			

		endswitch;



		WC_Admin_Settings::save_fields( $_SETTINGS );

	}
	
}
