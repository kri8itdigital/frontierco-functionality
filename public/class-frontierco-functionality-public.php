<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.kri8it.com
 * @since      1.0.0
 *
 * @package    Frontierco_Functionality
 * @subpackage Frontierco_Functionality/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Frontierco_Functionality
 * @subpackage Frontierco_Functionality/public
 * @author     Hilton Moore <hilton@kri8it.com>
 */
class Frontierco_Functionality_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Frontierco_Functionality_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Frontierco_Functionality_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/frontierco-functionality-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Frontierco_Functionality_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Frontierco_Functionality_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */


		$_ARRAY_OF_ARGS = array(
			'ajax_url' 					=> get_bloginfo('url').'/wp-admin/admin-ajax.php'
		);


		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/frontierco-functionality-public.js', array( 'jquery' ), $this->version, false );

		wp_localize_script( $this->plugin_name, 'frontierco_params', $_ARRAY_OF_ARGS );

	}









	/* OUTPUT FOR STORE PICKUP CHOICE */
	public function woocommerce_review_order_after_shipping(){
		
		$_SHIPPING = WC()->session->get( 'chosen_shipping_methods' );
		$_SHIPPING = reset($_SHIPPING);
		
		$_IS_PICKUP = str_contains($_SHIPPING, 'frontierco_store_pickup');

		if(is_checkout() && get_option('frontierco_functionality_enable_store_pickup') && $_IS_PICKUP && wp_doing_ajax()):

				$_PICKUPS = get_posts(array('post_type' => 'storepickup', 'posts_per_page' => '-1'));

				if(count($_PICKUPS) > 0):

					?>

					<tr><td colspan="2">

					<?php

					if ( isset( $_POST['post_data'] ) ):
						parse_str( $_POST['post_data'], $_DATA );
					endif;

					$_PICKUP_OPTIONS = array();

					$_PICKUP_OPTIONS[''] = get_option('frontierco_functionality_store_pickup_dropdown_placeholder');

					foreach($_PICKUPS as $_PU):
						$_PICKUP_OPTIONS[$_PU->ID] = $_PU->post_title;
					endforeach;

					woocommerce_form_field( 'frontierco_store_pickup', array(
					    	'type'          => 'select',
					    	'required'  	=> true,
					    	'options'		=> $_PICKUP_OPTIONS,
					    	'class'         => array('frontierco-store-pickup form-row-wide'),
					    	'label'         => __(get_option('frontierco_functionality_store_pickup_dropdown_label')),
					    ), $_DATA['frontierco_store_pickup']);

					WC()->session->set('SHOWING_FRONTIERCO_STORE_PICKUP', 'YES');

					?>

					</td></tr>

					<?php

			endif;

		endif;

	}









	/* VALIDATION FOR STORE PICKUP DETAILS */
	public function woocommerce_after_checkout_validation(){
		if(isset($_POST['frontierco_store_pickup'])):
			if (empty($_POST['frontierco_store_pickup']) || $_POST['frontierco_store_pickup'] == '') {
		         wc_add_notice( __( get_option('frontierco_functionality_store_pickup_dropdown_error') , 'frontierco-functionality' ), 'error' );
		    }
		endif;
	}









	/* SAVE THE TYPE OF STORE PICKUP IF IT IS USED */
	public function woocommerce_checkout_update_order_meta($_ORDER_ID, $_DATA){

		update_post_meta($_ORDER_ID, '_ORDER_DATA', $_DATA);
		update_post_meta($_ORDER_ID, '_ORDER_POST', $_POST);

		if(!empty($_POST['frontierco_store_pickup'])):
			update_post_meta($_ORDER_ID, '_frontierco_store_pickup', sanitize_text_field( $_POST['frontierco_store_pickup']));
		endif;

	}









	/* UPDATE SHIPPING DETAILS TO STORE DETAILS */
	public function woocommerce_checkout_order_created($_ORDER){

		$_ORDER_ID = $_ORDER->get_id();

		if(get_post_meta($_ORDER_ID, '_frontierco_store_pickup', true)):

			$_STORE = get_post_meta($_ORDER_ID, '_frontierco_store_pickup', true);

			$_STORE_OBJ = get_post($_STORE);

			$_STORE_NAME = $_STORE_OBJ->post_title;

			$_ORDER->set_shipping_company($_STORE_NAME);

			$_ORDER->save();			

		endif;

	}

}
