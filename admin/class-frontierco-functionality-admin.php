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
	public function admin_menu(){

		/* EXTEND PRODUCTS MENU FOR SORTING */
		if (FRONTIERCO::is_woocommerce_active()):

			add_submenu_page(
				'edit.php?post_type=product', 
				'FrontierCo Product Sort', 
				'FC Product Sort', 
				'edit_users', 
				'frontierco-product-sort', 
				array($this, 'product_sort_menu')
			);




			add_submenu_page(
				'edit.php?post_type=product', 
				'FrontierCo Hide Sale Items', 
				'FC Hide Sale Items', 
				'edit_users', 
				'frontierco-hide-sale-items', 
				array($this, 'hide_sale_items')
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
								jQuery('#frontieroverlay').addClass('show');

								jQuery.post( ajaxurl, {
									action: 'frontierco_update_product_order',
									order: jQuery('#sortable').sortable('serialize', { key: "sort" }),
									category: jQuery('#categorySelect').val()
								}).done(function(){ jQuery('#frontieroverlay').removeClass('show'); });

								
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

						<?php 

						$_DISPLAY = FRONTIERCO::get_product_display_name($_TERM);

						?>

							<option <?php selected($_SELECTED, $_TERM->slug); ?>value="<?php echo $_TERM->slug; ?>"><?php echo $_DISPLAY; ?></option>

						<?php endforeach; ?>
					</select>
				</form>
			</div>

			<?php if($_DO_LIST): ?>

				<div class="frontierco_page_content">
					
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

				</div>

			<?php endif; ?>
		</div>

		<?php
	}






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

					$_DISPLAY = FRONTIERCO::get_product_display_name($_TERM);

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
	public function frontierco_update_hide_sale(){

		
		$_ITEM = $_POST['term'];
		$_SHOW = $_POST['show'];

		$_RESULT = update_term_meta($_ITEM, 'frontierco_show_sale_items', $_SHOW);


		exit;



	}









	/* */
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









	/* */
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

	}









	/* LOADER INSIDE ADMIN */
	public function in_admin_header(){
		?>

		<div id="frontieroverlay"></div>

		<?php
	}

}
