<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * Dashboard. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://shop.stehle-internet.de/downloads/widget-visibility-time-scheduler-pro/
 * @since             1.0.0
 * @package           Hinjiwvtsp
 *
 * @wordpress-plugin
 * Plugin Name:       Widget Visibility Time Scheduler Pro
 * Plugin URI:        https://shop.stehle-internet.de/downloads/widget-visibility-time-scheduler-pro/
 * Description:       Control the visibility of each widget based on date, time and weekday easily.
 * Version:           5.2
 * Author:            Martin Stehle
 * Author URI:        http://stehle-internet.de/
 * License:           You may change and use it as often you want, but you must not sell it or must not give it away for free.
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       hinjiwvtsp
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The constants for this plugin
 */
define( 'WVTSP_ROOT_FILE', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-hinjiwvtsp-activator.php
 */
function activate_hinjiwvtsp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-hinjiwvtsp-activator.php';
	Hinjiwvtsp_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-hinjiwvtsp-deactivator.php
 */
function deactivate_hinjiwvtsp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-hinjiwvtsp-deactivator.php';
	Hinjiwvtsp_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_hinjiwvtsp' );
register_deactivation_hook( __FILE__, 'deactivate_hinjiwvtsp' );

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-hinjiwvtsp.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_hinjiwvtsp() {

	$plugin = new Hinjiwvtsp();
	$plugin->run();

}
run_hinjiwvtsp();
