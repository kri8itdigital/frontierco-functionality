<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.kri8it.com
 * @since      1.0.0
 *
 * @package    Frontierco_Functionality
 * @subpackage Frontierco_Functionality/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Frontierco_Functionality
 * @subpackage Frontierco_Functionality/includes
 * @author     Hilton Moore <hilton@kri8it.com>
 */
class Frontierco_Functionality {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Frontierco_Functionality_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'FRONTIERCO_FUNCTIONALITY_VERSION' ) ) {
			$this->version = FRONTIERCO_FUNCTIONALITY_VERSION;
		} else {
			$this->version = '2.0.0';
		}
		$this->plugin_name = 'frontierco-functionality';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Frontierco_Functionality_Loader. Orchestrates the hooks of the plugin.
	 * - Frontierco_Functionality_i18n. Defines internationalization functionality.
	 * - Frontierco_Functionality_Admin. Defines all hooks for the admin area.
	 * - Frontierco_Functionality_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * HELPER FUNCTIONS
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-frontierco-functionality-helpers.php';
		

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-frontierco-functionality-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-frontierco-functionality-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-frontierco-functionality-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-frontierco-functionality-public.php';

		$this->loader = new Frontierco_Functionality_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Frontierco_Functionality_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Frontierco_Functionality_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Frontierco_Functionality_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'admin_menu' );
		$this->loader->add_action( 'init', $plugin_admin, 'post_types' );

		$this->loader->add_filter( 'woocommerce_get_settings_pages', $plugin_admin, 'woocommerce_get_settings_pages' );

		$this->loader->add_action( 'wp_ajax_frontierco_update_product_order_cat', $plugin_admin, 'frontierco_update_product_order_cat'  );
		$this->loader->add_action( 'wp_ajax_nopriv_frontierco_update_product_order_cat', $plugin_admin, 'frontierco_update_product_order_cat'  );

		$this->loader->add_action( 'wp_ajax_frontierco_update_product_order_tag', $plugin_admin, 'frontierco_update_product_order_tag'  );
		$this->loader->add_action( 'wp_ajax_nopriv_frontierco_update_product_order_tag', $plugin_admin, 'frontierco_update_product_order_tag'  );

		$this->loader->add_action( 'wp_ajax_frontierco_update_hide_sale', $plugin_admin, 'frontierco_update_hide_sale'  );
		$this->loader->add_action( 'wp_ajax_frontierco_update_hide_sale_all', $plugin_admin, 'frontierco_update_hide_sale_all'  );

		$this->loader->add_action( 'pre_get_posts', $plugin_admin, 'parse_pre_query', 999, 1 );
		$this->loader->add_action( 'parse_query', $plugin_admin, 'parse_pre_query', 999, 1 );

		$this->loader->add_action( 'in_admin_header', $plugin_admin, 'in_admin_header', 99999, 1 );

		$this->loader->add_action( 'woocommerce_shipping_init', $plugin_admin, 'woocommerce_shipping_init', 99999);
		$this->loader->add_action( 'woocommerce_shipping_methods', $plugin_admin, 'woocommerce_shipping_methods', 1);

		$this->loader->add_action( 'wp_ajax_frontierco_selected_store_pickup', $plugin_admin, 'frontierco_selected_store_pickup'  );
		$this->loader->add_action( 'wp_ajax_nopriv_frontierco_selected_store_pickup', $plugin_admin, 'frontierco_selected_store_pickup'  );

		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'add_meta_boxes', 99999);
		$this->loader->add_action( 'save_post_storepickup', $plugin_admin, 'save_post', 99999, 1);
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Frontierco_Functionality_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_action( 'woocommerce_review_order_after_shipping', $plugin_public, 'woocommerce_review_order_after_shipping', 99);

		$this->loader->add_action( 'woocommerce_after_checkout_validation', $plugin_public, 'woocommerce_after_checkout_validation', 99);

		$this->loader->add_action( 'woocommerce_checkout_update_order_meta', $plugin_public, 'woocommerce_checkout_update_order_meta', 99, 2);

		$this->loader->add_action('woocommerce_checkout_order_created', $plugin_public, 'woocommerce_checkout_order_created', 99, 1);

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Frontierco_Functionality_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
