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









	/* SORT OUT THE ADMIN MENUS */
	public function admin_menu(){

		/* EXTEND PRODUCTS MENU FOR SORTING */
		if (FRONTIERCO::is_woocommerce_active()):


			
			add_submenu_page(
				'edit.php?post_type=product', 
				'FrontierCo Product Sort (CAT)', 
				'FC Product Sort (CAT)', 
				'manage_woocommerce', 
				'frontierco-product-sort-cat', 
				array($this, 'product_sort_menu_cat')
			);

			add_submenu_page(
				'edit.php?post_type=product', 
				'FrontierCo Product Sort (TAG)', 
				'FC Product Sort (TAG)', 
				'manage_woocommerce', 
				'frontierco-product-sort-tag', 
				array($this, 'product_sort_menu_tag')
			);

			add_submenu_page(
				'edit.php?post_type=product', 
				'FrontierCo Hide Sale Items', 
				'FC Hide Sale Items', 
				'manage_woocommerce', 
				'frontierco-hide-sale-items', 
				array($this, 'hide_sale_items')
			);
			
		endif;

	}









	/* SETUP THE STORE PICKUP POST TYPE */	
	public function post_types(){

		if(get_option('frontierco_functionality_enable_store_pickup')):

			$labels = array(
		    'name' => _x( 'Store Pickup', 'post type general name', 'frontierco-functionality' ),
		    'singular_name' => _x( 'Store Pickup', 'post type singular name', 'frontierco-functionality' ),
		    'add_new' => _x( 'Add New', 'storepickup', 'frontierco-functionality' ),
		    'add_new_item' => __( 'Add Store Pickup', 'frontierco-functionality' ),
		    'edit_item' => __( 'Edit Store Pickup', 'frontierco-functionality' ),
		    'new_item' => __( 'New Store Pickup', 'frontierco-functionality' ),
		    'view_item' => __( 'View Store Pickup', 'frontierco-functionality' ),
		    'search_items' => __( 'Search Store Pickups', 'frontierco-functionality' ),
		    'not_found' =>  __( 'No Store Pickups found', 'frontierco-functionality' ),
		    'not_found_in_trash' => __( 'No Store Pickups found in Trash', 'frontierco-functionality' ), 
		    'parent_item_colon' => ''
		  );
		  
		  $rewrite = 'store-pickup';
		  
		  $args = array(
		    'labels' => $labels,
		    'public' => false,
		    'publicly_queryable' => true,
		    'show_ui' => true, 
		    'query_var' => true,
		    'rewrite' => array( 'slug' => $rewrite ),
		    'capability_type' => 'post',
		    'hierarchical' => false,
		    'menu_position' => null, 
		    'menu_icon' => 'dashicons-admin-multisite',
		    'has_archive' => false, 
		    'show_in_rest' => false,
		    'supports' => array('title'),
    		'taxonomies' => array( 'province'),
			'map_meta_cap' => true 
		  );
		      
		  register_post_type( 'storepickup', $args );

		endif;

	}









	/* WOOCOMMERCE SETTINGS PAGE */
	public function woocommerce_get_settings_pages($_SETTINGS){

		include plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-frontierco-functionality-settings.php';

		$_SETTINGS[] = new Frontierco_Functionality_Settings();

		return $_SETTINGS;

	}









	/* MENU CALLBACK - CAT */
	public function product_sort_menu_cat(){

		wp_enqueue_script( 'jquery-ui-sortable' );

		$_TERMS = FRONTIERCO::get_product_cats();

		$_DO_LIST = false;

		$_PRODUCTS = false;

		if(isset($_GET['sort-category'])):

			$_SELECTED = $_GET['sort-category'];
			$_DO_LIST = true;
			$_PRODUCTS = FRONTIERCO::get_products_from_cat($_SELECTED);

		else:

			$_SELECTED = '';

		endif;

		?>

		<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
		<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

		<script type="text/javascript">
		
			jQuery(document).ready(function(){

				jQuery('#categorySelect').on('change', function(){

					jQuery('#categoryForm').submit();

				});

				jQuery('#categorySelect').select2();

				if(jQuery('#sortable').length){
					jQuery('#sortable').sortable();
				}


				jQuery('#SaveSort').on('click', function(){

					jQuery('#frontieroverlay').addClass('show');

					jQuery.post( ajaxurl, {
						action: 'frontierco_update_product_order_cat',
						order: jQuery('#sortable').sortable('serialize', { key: "sort" }),
						category: jQuery('#categorySelect').val()
					}).done(function(){ jQuery('#frontieroverlay').removeClass('show'); });


				});


			});

		</script>

		<div class="wrap frontierco_page">
			<div class="frontierco_page_header">
				<h2>FrontierCo Product Sort (Cat)</h2>	
			</div>

			<div class="frontierco_page_selection">
				<form id="categoryForm" method="get" action="edit.php?post_type=product&page=frontierco-product-sort-cat">
					<input type="hidden" name="post_type" value="product" />
					<input type="hidden" name="page" value="frontierco-product-sort-cat" />

					<select id="categorySelect" name="sort-category">

						<option value="">- Select a Product Category -</option>

						<?php foreach($_TERMS as $_TERM): ?>

						<?php  $_DISPLAY = FRONTIERCO::get_cat_display_name($_TERM); ?>

							<option <?php selected($_SELECTED, $_TERM->slug); ?>value="<?php echo $_TERM->slug; ?>"><?php echo $_DISPLAY; ?></option>

						<?php endforeach; ?>
					</select>
				</form>
			</div>

			<?php if($_DO_LIST): ?>

				<div class="frontierco_page_content">
					<?php if(is_array($_PRODUCTS) && count($_PRODUCTS)> 0): ?>
					<ul id="sortable">

						<?php foreach($_PRODUCTS as $_PROD): ?>

							<?php $_THE_PROD = wc_get_product($_PROD->ID); ?>

							<li id="sort_<?php echo $_PROD->ID; ?>">
								<div class="sort_item_container">
									<div class="sort_item_image">
										<img src="<?php echo wp_get_attachment_url( $_THE_PROD->get_image_id() ); ?>" />
									</div>
									<div class="sort_item_title">
										#<?php echo $_PROD->ID; ?><span>: <?php echo $_THE_PROD->get_name(); ?></span> (<?php echo $_THE_PROD->get_sku(); ?>) | <?php echo $_THE_PROD->get_price_html(); ?>
									</div>
								</div>
							</li>

						<?php endforeach; ?>

					</ul>


						
					<?php else: ?>

						<p class="frontierco_error">No Products Found</p>

					<?php endif; ?>

				</div>

				<?php if(is_array($_PRODUCTS) && count($_PRODUCTS)> 0): ?>
					<div class="frontierco_page_save">
						
						<a class="frontierco_save_sort_btn" id="SaveSort">SAVE ORDER</a>

					</div>
				<?php endif; ?>

			<?php endif; ?>
		</div>

		<?php
	}









	/* MENU CALLBACK - TAG */
	public function product_sort_menu_tag(){

		wp_enqueue_script( 'jquery-ui-sortable' );

		$_TERMS = FRONTIERCO::get_product_tags();

		$_DO_LIST = false;

		$_PRODUCTS = false;

		if(isset($_GET['sort-tag'])):

			$_SELECTED = $_GET['sort-tag'];
			$_DO_LIST = true;
			$_PRODUCTS = FRONTIERCO::get_products_from_tag($_SELECTED);

		else:

			$_SELECTED = '';

		endif;

		?>

		<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
		<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

		<script type="text/javascript">
		
			jQuery(document).ready(function(){


				jQuery('#tagSelect').on('change', function(){

					jQuery('#tagForm').submit();

				});

				jQuery('#tagSelect').select2();

				if(jQuery('#sortable').length){

					jQuery('#sortable').sortable();

				}


				jQuery('#SaveSort').on('click', function(){

					jQuery('#frontieroverlay').addClass('show');

					jQuery.post( ajaxurl, {
						action: 'frontierco_update_product_order_tag',
						order: jQuery('#sortable').sortable('serialize', { key: "sort" }),
						tag: jQuery('#tagSelect').val()
					}).done(function(){ jQuery('#frontieroverlay').removeClass('show'); });


				});


			});

		</script>

		<div class="wrap frontierco_page">
			<div class="frontierco_page_header">
				<h2>FrontierCo Product Sort (Tag)</h2>	
			</div>

			<div class="frontierco_page_selection">
				<form id="tagForm" method="get">
					<input type="hidden" name="post_type" value="product" />
					<input type="hidden" name="page" value="frontierco-product-sort-tag" />
					<select id="tagSelect" name="sort-tag">

						<option value="">- Select a Product Tag -</option>

						<?php foreach($_TERMS as $_TERM): ?>

						<?php $_DISPLAY = FRONTIERCO::get_tag_display_name($_TERM); ?>

							<option <?php selected($_SELECTED, $_TERM->slug); ?>value="<?php echo $_TERM->slug; ?>"><?php echo $_DISPLAY; ?></option>

						<?php endforeach; ?>
					</select>
				</form>
			</div>

			<?php if($_DO_LIST): ?>

				<div class="frontierco_page_content">
					
					<?php if(is_array($_PRODUCTS) && count($_PRODUCTS)> 0): ?>
						<ul id="sortable">

							<?php foreach($_PRODUCTS as $_PROD): ?>

								<?php $_THE_PROD = wc_get_product($_PROD->ID); ?>

								<li id="sort_<?php echo $_PROD->ID; ?>">
									<div class="sort_item_container">
										<div class="sort_item_image">
											<img src="<?php echo wp_get_attachment_url( $_THE_PROD->get_image_id() ); ?>" />
										</div>
										<div class="sort_item_title">
											#<?php echo $_PROD->ID; ?><span>: <?php echo $_THE_PROD->get_name(); ?></span> (<?php echo $_THE_PROD->get_sku(); ?>) | <?php echo $_THE_PROD->get_price_html(); ?>
										</div>
									</div>
								</li>

							<?php endforeach; ?>

						</ul>

					<?php else: ?>

						<p class="frontierco_error">No Products Found</p>

					<?php endif; ?>

				</div>


				<?php if(is_array($_PRODUCTS) && count($_PRODUCTS)> 0): ?>
					<div class="frontierco_page_save">
						
						<a class="frontierco_save_sort_btn" id="SaveSort">SAVE ORDER</a>

					</div>
				<?php endif; ?>

			<?php endif; ?>
		</div>

		<?php
	}









	/* MENU CALLBACK - SALES */
	public function hide_sale_items(){


		$_TERMS = FRONTIERCO::get_product_cats();

		?>

		

		<script type="text/javascript">
		
			jQuery(document).ready(function(){


				jQuery('.hide_sale_changer').on('change', function(){
					jQuery('#frontieroverlay').addClass('show');
					
					jQuery.post( ajaxurl, {
						action: 'frontierco_update_hide_sale',
						term: jQuery(this).attr('data-item'),
						show: jQuery(this).val()
					}).done(function(){ jQuery('#frontieroverlay').removeClass('show'); });
				});

				jQuery('.frontierco_update_hide_sale_all').on('click', function(){
					jQuery('#frontieroverlay').addClass('show');
					
					jQuery.post( ajaxurl, {
						action: 'frontierco_update_hide_sale_all',
						show: jQuery(this).attr('data-value')
					}).done(function(){ 

						setTimeout(function(){window.location.reload(true);}, 3000);
						

					});
				});


			});

		</script>

		<div class="wrap frontierco_page">
			<div class="frontierco_page_header">
				<h2>FrontierCo Hide Sale Items</h2>	
			</div>

			<div class="fontierco_page_actions">
				<a class="frontierco_update_hide_sale_all" data-value="yes">Mark All 'Yes'</a> | <a class="frontierco_update_hide_sale_all" data-value="no">Mark All 'No'</a>
			</div>

			<div class="fontierco_page_content">
				
				<?php foreach($_TERMS as $_TERM): ?>

					<?php 

					$_DISPLAY = FRONTIERCO::get_cat_display_name($_TERM);

					$_VALUE = get_term_meta($_TERM->term_id, 'frontierco_show_sale_items', true);

					?>

					<div class="category_sale_item">
						<div class="category_name"><?php echo $_DISPLAY; ?></div>
						<div class="category_value">
							<select class="hide_sale_changer" data-item="<?php echo $_TERM->term_id; ?>">
								<option <?php selected($_VALUE, 'no'); ?> value="no">No</option>
								<option <?php selected($_VALUE, 'yes'); ?> value="yes">Yes</option>
							</select>
						</div>
					</div>

				<?php endforeach; ?>

			</div>

		</div>

		<?php

	}









	/* AJAX TO UPDATE ORDERING - CAT*/
	public function frontierco_update_product_order_cat(){

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









	/* AJAX TO UPDATE ORDERING - TAG*/
	public function frontierco_update_product_order_tag(){

		$_DATA = explode("&", $_POST['order']);

		$_KEY = 'tag_ordering_'.$_POST['tag'];

		$_COUNT = 1;

		foreach($_DATA as $_ITEM):

			$_ID = (int)str_replace("sort=", "", $_ITEM);

			update_post_meta($_ID, $_KEY, $_COUNT);

			$_COUNT++;

		endforeach;

		exit;



	}









	/* AJAX TO UPDATE SALE*/
	public function frontierco_update_hide_sale(){

		
		$_ITEM = $_POST['term'];
		$_SHOW = $_POST['show'];

		$_RESULT = update_term_meta($_ITEM, 'frontierco_show_sale_items', $_SHOW);


		exit;



	}









	/* AJAX TO UPDATE SALE*/
	public function frontierco_update_hide_sale_all(){

		$_SHOW = $_POST['show'];

		$_TERMS = FRONTIERCO::get_product_cats();

		foreach($_TERMS as $_TERM):

			$_ITEM = $_TERM->term_id;

			update_term_meta($_ITEM, 'frontierco_show_sale_items', $_SHOW);

		endforeach;

		sleep(5);


		exit;



	}









	/* FRONTEND HANDLING OF ALL THINGS */
	public function parse_pre_query($_QUERY){

		if(is_product_category() && !is_admin()):

			$_TAX = get_queried_object();

			$_KEY = 'cat_ordering_'.$_TAX->slug;

			if(!isset($_GET['orderby']) || $_GET['orderby'] == 'menu_order' || $_QUERY->get('orderby') == 'menu_order' || !$_QUERY->get('orderby') || $_QUERY->get('orderby') == ''):


				$_META_QUERY = $_QUERY->get('meta_query');

				if(!is_array($_META_QUERY)): $_META_QUERY = array(); endif;

				if($_QUERY->get('frontierco_cat_ordering') != 'yes'):
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

					$_QUERY->set('frontierco_cat_ordering', 'yes');

					$_QUERY->set('orderby', 'meta_value_num menu_order');
					$_QUERY->set('order', 'ASC');


				endif;

				if(get_term_meta($_TAX->term_id, 'frontierco_show_sale_items', true) == 'yes'):
					$_QUERY->set('post__not_in', wc_get_product_ids_on_sale());
				endif;



				$_QUERY->set('meta_query', $_META_QUERY);

				


			endif;


		endif;




		if(is_product_tag() && !is_admin()):


			$_TAX = get_queried_object();

			$_KEY = 'tag_ordering_'.$_TAX->slug;

			if(!isset($_GET['orderby']) || $_GET['orderby'] == 'menu_order' || $_QUERY->get('orderby') == 'menu_order' || !$_QUERY->get('orderby') || $_QUERY->get('orderby') == ''):


				$_META_QUERY = $_QUERY->get('meta_query');

				if(!is_array($_META_QUERY)): $_META_QUERY = array(); endif;

				if($_QUERY->get('frontierco_tag_ordering') != 'yes'):
					$_META_QUERY[]=	array(
						'tag_ordering'  => array(
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

					$_QUERY->set('frontierco_tag_ordering', 'yes');

					$_QUERY->set('orderby', 'meta_value_num menu_order');
					$_QUERY->set('order', 'ASC');


				endif;

				if(get_term_meta($_TAX->term_id, 'frontierco_show_sale_items', true) == 'yes'):
					$_QUERY->set('post__not_in', wc_get_product_ids_on_sale());
				endif;

				$_QUERY->set('meta_query', $_META_QUERY);

			endif;


		endif;

	}









	/* LOADER INSIDE ADMIN */
	public function in_admin_header(){
		?>

		<div id="frontieroverlay"></div>

		<?php
	}









	/* INCLUDE OUR SHIPPING METHOD */
	public function woocommerce_shipping_init(){

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-frontierco-functionality-storepickup.php';
	}









	/* ADD OUR SHIPPING METHOD */
	public function woocommerce_shipping_methods($_METHODS){

		if(get_option('frontierco_functionality_enable_store_pickup')):

			$_METHODS['frontierco_store_pickup'] = 'WC_Shipping_FrontierCo_Store_Pickup';

		endif;

		return $_METHODS;
	}









	/* AJAX FUNCTION FOR STORE PICKUP DETAILS */
	public function frontierco_selected_store_pickup(){
		$_STORE = $_POST['store'];

		$_ARRAY = array(
			'shipping_address_1' 	=> get_post_meta($_STORE, 'store_pickup_shipping_address_1', true),
			'shipping_address_2' 	=> get_post_meta($_STORE, 'store_pickup_shipping_address_2', true),
			'shipping_city' 		=> get_post_meta($_STORE, 'store_pickup_shipping_city', true),
			'shipping_postcode' 	=> get_post_meta($_STORE, 'store_pickup_shipping_postcode', true),
			'shipping_country' 		=> get_post_meta($_STORE, 'store_pickup_shipping_country', true),
			'shipping_state' 		=> get_post_meta($_STORE, 'store_pickup_shipping_state', true)
		);

		echo json_encode($_ARRAY);
		die();
	}









	/* ADD META BOXES FOR STORE DETAILS */
	public function add_meta_boxes(){

		add_meta_box(
			'frontierco_store_pickup_meta',
			'Store Pickup Details',
			array($this, 'store_pickup_meta_details'),
			'storepickup'
		);

	}









	/* META CALLBACK FOR DISPLAY */
	public function store_pickup_meta_details(){

		global $post;

		?>

		<div id="frontierco_store_pickup_meta">

			<div class="frontierco_store_pickup_meta_row">
				<label for="store_pickup_shipping_address_1"><strong>Shipping Address Line 1<sup>*</sup></strong></label>
				<input required value="<?php echo get_post_meta($post->ID, 'store_pickup_shipping_address_1', true); ?>" type="text" id="store_pickup_shipping_address_1" name="store_pickup_shipping_address_1" />
			</div>

			<div class="frontierco_store_pickup_meta_row">
				<label for="store_pickup_shipping_address_2"><strong>Shipping Address Line 2<sup>*</sup></strong></label>
				<input required value="<?php echo get_post_meta($post->ID, 'store_pickup_shipping_address_2', true); ?>" type="text" id="store_pickup_shipping_address_2" name="store_pickup_shipping_address_2" />
			</div>
			<div class="frontierco_store_pickup_meta_row">
				<label for="store_pickup_shipping_city"><strong>Shipping Address City<sup>*</sup></strong></label>
				<input required value="<?php echo get_post_meta($post->ID, 'store_pickup_shipping_city', true); ?>" type="text" id="store_pickup_shipping_city" name="store_pickup_shipping_city" />
			</div>

			<div class="frontierco_store_pickup_meta_row">
				<label for="store_pickup_shipping_postcode"><strong>Shipping Address Post Code<sup>*</sup></strong></label>
				<input required value="<?php echo get_post_meta($post->ID, 'store_pickup_shipping_postcode', true); ?>" type="text" id="store_pickup_shipping_postcode" name="store_pickup_shipping_postcode" />
			</div>

			<div class="frontierco_store_pickup_meta_row">
				<label for="store_pickup_shipping_country"><strong>Shipping Address Country<sup>*</sup></strong></label>
				<input required value="<?php echo get_post_meta($post->ID, 'store_pickup_shipping_country', true); ?>" type="text" id="store_pickup_shipping_country" name="store_pickup_shipping_country" />
			</div>

			<div class="frontierco_store_pickup_meta_row">
				<label for="store_pickup_shipping_state"><strong>Shipping Address State<sup>*</sup></strong></label>
				<input required value="<?php echo get_post_meta($post->ID, 'store_pickup_shipping_state', true); ?>" type="text" id="store_pickup_shipping_state" name="store_pickup_shipping_state" />
			</div>
		</div>
		<?php

	}









	/* SAVE THE META */
	public function save_post($_POST_ID){

		update_post_meta(
			$_POST_ID,
			'store_pickup_shipping_address_1',
			$_POST['store_pickup_shipping_address_1']
		);

		update_post_meta(
			$_POST_ID,
			'store_pickup_shipping_address_2',
			$_POST['store_pickup_shipping_address_2']
		);

		update_post_meta(
			$_POST_ID,
			'store_pickup_shipping_city',
			$_POST['store_pickup_shipping_city']
		);

		update_post_meta(
			$_POST_ID,
			'store_pickup_shipping_postcode',
			$_POST['store_pickup_shipping_postcode']
		);

		update_post_meta(
			$_POST_ID,
			'store_pickup_shipping_country',
			$_POST['store_pickup_shipping_country']
		);

		update_post_meta(
			$_POST_ID,
			'store_pickup_shipping_state',
			$_POST['store_pickup_shipping_state']
		);

	}

}
