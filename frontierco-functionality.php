<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.kri8it.com
 * @since             1.0.2
 * @package           Frontierco_Functionality
 *
 * @wordpress-plugin
 * Plugin Name:       FrontierCo Functionality
 * Plugin URI:        https://www.kri8it.com
 * Description:       This plugin adds FrontierCo specific functionality and requirements.
 * Version:           1.2.2
 * Author:            Hilton Moore
 * Author URI:        https://www.kri8it.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       frontierco-functionality
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'FRONTIERCO_FUNCTIONALITY_VERSION', '1.2.2' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-frontierco-functionality-activator.php
 */
function activate_frontierco_functionality() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-frontierco-functionality-activator.php';
	Frontierco_Functionality_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-frontierco-functionality-deactivator.php
 */
function deactivate_frontierco_functionality() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-frontierco-functionality-deactivator.php';
	Frontierco_Functionality_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_frontierco_functionality' );
register_deactivation_hook( __FILE__, 'deactivate_frontierco_functionality' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-frontierco-functionality.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_frontierco_functionality() {

	$plugin = new Frontierco_Functionality();
	$plugin->run();

}





add_action( 'plugins_loaded', 'frontierco_functionality_check_for_update' );
function frontierco_functionality_check_for_update(){

    require_once plugin_dir_path( __FILE__ ) . 'includes/class-frontierco-functionality-updater.php';


      $config = array(
            'slug'               => plugin_basename( __FILE__ ),
            'proper_folder_name' => 'frontierco-functionality',
            'api_url'            => 'https://api.github.com/repos/kri8itdigital/frontierco-functionality',
            'raw_url'            => 'https://raw.github.com/kri8itdigital/frontierco-functionality/master',
            'github_url'         => 'https://github.com/kri8itdigital/frontierco-functionality',
            'zip_url'            => 'https://github.com/kri8itdigital/frontierco-functionality/archive/master.zip',
            'homepage'           => 'https://github.com/kri8itdigital/frontierco-functionality',
            'sslverify'          => true,
            'requires'           => '5.0',
            'tested'             => '5.9.3',
            'readme'             => 'README.md',
            'version'            => '1.2.2'
        );

        new Frontierco_Functionality_Updater( $config );

}




run_frontierco_functionality();
