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
			'' => __( 'Settings', 'frontierco-functionality' ),
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

				$_ATTRIBUTES = wc_get_attribute_taxonomies();

				$_SELECTION = array();

				foreach($_ATTRIBUTES as $_ATT):

					$_SELECTION[$_ATT->attribute_name] = $_ATT->attribute_label;

				endforeach;

				$_SETTINGS = array(
					array(
						'title' => __( 'FrontierCo General Settings', 'frontierco-functionality' ),
						'type'  => 'title',
						'id'    => 'frontierco_settings',
					),
					array(
						'title'         => __( 'Section Options', 'fincon-woocommerce' ),
						'desc'          => __( 'Show Variations on Frontend', 'fincon-woocommerce' ),
						'id'            => 'frontierco_functionality_show_variations_on_front',
						'default'       => 'no',
						'checkboxgroup' => 'start',
						'type'          => 'checkbox'
					),
					array(
						'desc'          => __( 'Hide Parent Variation', 'fincon-woocommerce' ),
						'id'            => 'frontierco_functionality_hide_parent_variation',
						'default'       => 'no',
						'checkboxgroup' => 'end',
						'type'          => 'checkbox'
					),
					array(
							'title' 	=> __('Attribute To Use', 'fincon-woocommerce'),
							'desc' 		=> __('The attribute to use for showing on the front end', 'fincon-woocommerce' ),
							'type' 		=> 'select',
							'default' 	=> '',
							'id' 		=> 'frontierco_functionality_variation_attribute',
							'default' 	=> 'None',
							'options'   => $_SELECTION
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
