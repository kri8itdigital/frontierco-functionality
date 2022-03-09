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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/frontierco-functionality-public.js', array( 'jquery' ), $this->version, false );

	}









	/* INCLUDE ELEMENTOR FUNCTIONALITY */
	public function plugins_loaded(){
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-frontierco-functionality-elementor.php';
	}









	/* INCLUDE ELEMENTOR CATEGORY */
	public function categories_registered($elements_manager){

		$elements_manager->add_category(
			'frontierco',
			[
				'title' => esc_html__( 'FrontierCo', 'frontierco-functionality' ),
				'icon' => 'fa fa-mountains',
			]
		);

	}




	public function woocommerce_product_query($_QUERY){

		$original_post_types = (array) $_QUERY->get('post_type');
		if(!empty($original_post_types)) {
   			$_QUERY->set('post_type', array_merge( $original_post_types, array('product','product_variation') ) );
   		} else {
   			$_QUERY->set('post_type', array('product','product_variation'));	
   		}

   		if(get_option('frontierco_functionality_hide_parent_variation') == 'yes'){

	   		$tax_query = array(
			    'relation' => 'AND'
			);

		    if(isset($query->tax_query) && isset($query->tax_query->queries) && !empty($query->tax_query->queries)) {
		    	$tax_query = array_merge($tax_query, $query->tax_query->queries);
		    }

		    $tax_query[] = array(
		        'taxonomy'        =>  'product_type',
		        'field'           =>  'slug',
		        'terms'           =>  'variable'
		    );

		    $tax_query[] = array(
		        'taxonomy'        =>  'product_type',
		        'field'           =>  'slug',
		        'terms'           =>  'variable'
		    );

		    $excludedAttributeProductsQuery = array(
			   	'post_type'      => array('product'),
			   	// use title
			   	'orderby'		=> 'menu_order',
			   	'order'			=> 'ASC',
			   	'post_status'    => 'publish',
			   	'posts_per_page' => -1,
			   	'tax_query'      => $tax_query,
			);

		    $products = new WP_Query( $excludedAttributeProductsQuery );	

		    foreach ($products->posts as $parentProductID) {

				$parentProductID = $parentProductID->ID;
				$parent_product = wc_get_product($parentProductID);
				if(!$parent_product) {
					continue;
				}

				$variation_ids = $parent_product->get_children();
				if(empty($variation_ids)) {
					continue;
				}

			}

	   	}


	}

}
