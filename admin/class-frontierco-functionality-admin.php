<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.kri8it.com
 * @since      1.0.0
 *
 * @package    Frontierco_Functionality
 * @subpackage Frontierco_Functionality/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Frontierco_Functionality
 * @subpackage Frontierco_Functionality/admin
 * @author     Hilton Moore <hilton@kri8it.com>
 */
class Frontierco_Functionality_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/frontierco-functionality-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/frontierco-functionality-admin.js', array( 'jquery' ), $this->version, false );

	}









	/* */	
	public function get_settings_pages($settings){


		include plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-frontierco-functionality-settings.php';

		$settings[] = new Frontierco_Functionality_Settings();


		return $settings;

	}









	/* */	
	public function admin_menu(){

		/* EXTEND PRODUCTS MENU FOR SORTING */
		if (FRONTIERCO::is_woocommerce_active()):

			add_submenu_page(
				'edit.php?post_type=product', 
				'FrontierCo Product Sort', 
				'Product Sort', 
				'edit_users', 
				'frontierco-product-sort', 
				array($this, 'product_sort_menu')
			);

		endif;

	}









	/* */
	public function product_sort_menu(){

		wp_enqueue_script( 'jquery-ui-sortable' );

		$_TERMS = FRONTIERCO::get_product_cats();

		$_DO_LIST = false;

		$_PRODUCTS = false;

		if(isset($_GET['category'])):

			$_SELECTED = $_GET['category'];
			$_DO_LIST = true;
			$_PRODUCTS = FRONTIERCO::get_products_from_cat($_SELECTED);

		else:

			$_SELECTED = '';

		endif;

		?>

		<script type="text/javascript">
		
			jQuery(document).ready(function(){


				jQuery('#categorySelect').on('change', function(){

					jQuery('#categoryForm').submit();

				});


				if(jQuery('#sortable').length){
					jQuery('#sortable').sortable(
						{
							'update' : function(e, ui) {								
								jQuery.post( ajaxurl, {
									action: 'frontierco_update_product_order',
									order: jQuery('#sortable').sortable('serialize', { key: "sort" }),
									category: jQuery('#categorySelect').val()
								});
							}
						}
					);
				}


			});

		</script>

		<div class="wrap frontierco_page">
			<div class="frontierco_page_header">
				<h2>FrontierCo Product Sort</h2>	
			</div>

			<div class="frontierco_page_selection">
				<form id="categoryForm" method="get">
					<input type="hidden" name="post_type" value="product" />
					<input type="hidden" name="page" value="frontierco-product-sort" />
					<select id="categorySelect" name="category">

						<option value="">- Select a Product Category -</option>

						<?php foreach($_TERMS as $_TERM): ?>

							<option <?php selected($_SELECTED, $_TERM->slug); ?>value="<?php echo $_TERM->slug; ?>"><?php echo $_TERM->name; ?></option>

						<?php endforeach; ?>
					</select>
				</form>
			</div>

			<?php if($_DO_LIST): ?>

				<div class="frontierco_page_content">
					
					<ul id="sortable">

						<?php foreach($_PRODUCTS as $_PROD): ?>

							<?php $_THE_PROD = wc_get_product($_PROD->ID); ?>

							<li id="sort_<?php echo $_PROD->ID; ?>">#<?php echo $_PROD->ID; ?><span>: <?php echo $_THE_PROD->get_name(); ?></span> (<?php echo $_THE_PROD->get_sku(); ?>)</li>

						<?php endforeach; ?>

					</ul>

				</div>

			<?php endif; ?>
		</div>

		<?php
	}









	/* */
	public function frontierco_update_product_order(){

		$_DATA = explode("&", $_POST['order']);

		$_KEY = 'cat_ordering_'.$_POST['category'];

		$_COUNT = 1;

		foreach($_DATA as $_ITEM):

			$_ID = (int)str_replace("sort=", "", $_ITEM);

			update_post_meta($_ID, $_KEY, $_COUNT);

			$_COUNT++;

		endforeach;

		exit;



	}









	/* */
	public function parse_pre_query($_QUERY){

		if(is_product_category()):

			//$_QUERY->set( 'post_type', array( 'product', 'product_variation' ));

			$_TAX = get_queried_object();

			$_KEY = 'cat_ordering_'.$_TAX->slug;

			if(!isset($_GET['orderby']) || $_GET['orderby'] == 'menu_order' || $_QUERY->get('orderby') == 'menu_order' || !$_QUERY->get('orderby') || $_QUERY->get('orderby') == ''):


				$_META_QUERY = $_QUERY->get('meta_query');

				if(!is_array($_META_QUERY)): $_META_QUERY = array(); endif;

				$_META_QUERY[]=	array(
					'cat_ordering'  => array(
						'relation' => 'OR',
						array(
							'key' => $_KEY,
							'compare' => 'EXISTS'
							),
						array(
							'key' => $_KEY,
							'compare' => 'NOT EXISTS'
						)
					)
				);

				$_QUERY->set('meta_query', $_META_QUERY);

				$_QUERY->set('orderby', 'meta_value_num menu_order');
				$_QUERY->set('order', 'ASC');


			endif;

		endif;

	}








	public function plugins_loaded(){
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-frontierco-functionality-elementor.php';
	}




	public function woocommerce_save_product_variation($variation_id, $i){
		if(!get_option('frontierco_functionality_show_variations_on_front') == 'yes'){
			return;
		}

		if(empty($variation_id)) {
			return;
		}

		if(!isset($_POST['product_id'])){
			return;
		}

		$product_id = absint($_POST['product_id']);
		$parent_product = wc_get_product( $product_id );
		if(!$parent_product) {
			return;
		}

		if(!isset($_POST['product_id']) || !isset($_POST['product-type']) || !isset($_POST['variable_post_id'])) {
			return;
		}

		if(!empty($_POST['variation_title'][$i])){
			update_post_meta($variation_id,'variation_title', sanitize_text_field($_POST['variation_title'][$i]));
		} else {
			update_post_meta($variation_id,'variation_title', '');
		}

		$variation_ids = $_POST['variable_post_id'];
		$variation_order = $_POST['variation_menu_order'];

		$product_id = absint($_POST['product_id']);
		$parent_product = wc_get_product( $product_id );
		if(!$parent_product) {
			return;
		}

		$parent_product_order = $parent_product->get_menu_order();
		$parent_product_status = $parent_product->get_status();

		foreach ($variation_ids as $index => $variation_id) {

			$variation = new WC_Product_Variation($variation_id);
			if(!$variation) {
				continue;
			}

			if($parent_product_status !== "auto-draft" && $parent_product_status !== "draft") {
				$variation->set_status( $parent_product->get_status() );
			} else {
				$variation->set_status( 'private' );
			}

			$variation->save();

			delete_post_meta($variation_id, 'frontierco_variation_updated');
		}

		$this->frontierco_update_variations(false);

	}


	public function transition_post_status($new_status, $old_status, $post){

		if(!get_option('frontierco_functionality_show_variations_on_front') == 'yes'){
			return;
		}

		if(!in_array( $post->post_type, array( 'product') ) ) {
 			return;
 		}

 		if(!isset($post->ID) || empty($post->ID)) {
 			return;
 		}

 		$product_id = $post->ID;

		$parent_product = wc_get_product( $product_id );
		if(!$parent_product) {
			return;
		}

		if(!$parent_product->is_type('variable')) {
			return;
		}

		$variation_ids = $parent_product->get_children();
		if(empty($variation_ids)) {
			return;
		}

		foreach ($variation_ids as $index => $variation_id) {

			$variation = new WC_Product_Variation($variation_id);
			if(!$variation) {
				continue;
			}

			delete_post_meta($variation_id, 'frontierco_variation_updated');
		}

		$this->frontierco_update_variations(false);
	}




	function frontierco_update_variations(){

		$_THE_VARIATION = get_option('frontierco_functionality_variation_attribute');


		$args = array(
		   	'post_type' => 'product_variation',
		   	'posts_per_page' => -1,
		   	'post_status' => 'any',
	   	);

	   	$posts = get_posts($args);
		foreach ($posts as $post) {

			$variation_id = $post->ID;
			$parent_product_id = wp_get_post_parent_id( $variation_id );
	        if( !$parent_product_id ) {
	        	continue;
        	} 

        	$parent_product = wc_get_product( $parent_product_id );
			if(!$parent_product) {
				continue;
			}

			$checkUpdated = get_post_meta($variation_id, 'frontierco_variation_updated', true);
			if($checkUpdated) {
				continue;
			}

			update_post_meta($variation_id, 'frontierco_variation_updated', true);

			$variation = new WC_Product_Variation($variation_id);
			if(!$variation) {
				return;
			}

			$taxonomies = array(
                'product_cat',
                'product_tag'
            );

            $taxonomies = apply_filters( 'woocommerce_single_variations_taxonomies', $taxonomies );

            foreach( $taxonomies as $taxonomy ) {
                $terms = (array) wp_get_post_terms( $parent_product_id, $taxonomy, array("fields" => "ids") );
                wp_set_post_terms( $variation_id, $terms, $taxonomy );

            }

			$attributes = $variation->get_variation_attributes();
            if(!empty($attributes)){
                foreach ($attributes as $key => $term) {

                    $attr_tax = urldecode( str_replace('attribute_', '', $key) );

                    if($_THE_VARIATION == $attr_tax){                    
	                    wp_set_post_terms($variation_id, $term, $attr_tax);
	                    
	                }
                }
            }

            $parent_product_status = $parent_product->get_status();
            if($parent_product_status !== "auto-draft" && $parent_product_status !== "draft") {
            	$variation->set_status( $parent_product->get_status() );
            } else {
				$variation->set_status( 'private' );
			}

            $dateCreated = $parent_product->get_date_created();

            $variation->set_date_created($dateCreated);
            $variation->save();

        }

	}

}
