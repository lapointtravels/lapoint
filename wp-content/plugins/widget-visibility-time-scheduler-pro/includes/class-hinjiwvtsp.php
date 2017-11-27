<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 *
 * @link       http://stehle-internet.de/downloads/widget-visibility-time-scheduler-pro
 * @since      1.0.0
 *
 * @package    Hinjiwvtsp
 * @subpackage Hinjiwvtsp/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Hinjiwvtsp
 * @subpackage Hinjiwvtsp/includes
 * @author     Martin Stehle <m.stehle@gmx.de>
 */
class Hinjiwvtsp {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Hinjiwvtsp_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The ID of this plugin.
	 *
	 * @since    3.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The slug of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_slug    The slug of this plugin.
	 */
	private $plugin_slug;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_version    The current version of the plugin.
	 */
	protected $plugin_version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the Dashboard and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'Widget Visibility Time Scheduler Pro';
		$this->plugin_slug = 'hinjiwvtsp';
		$this->plugin_version = '5.2';

		$this->load_dependencies();
		$this->set_locale();
		if ( is_admin() ) {
			$this->define_admin_hooks();
		} else {
			$this->define_public_hooks();
		}

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Hinjiwvtsp_Loader. Orchestrates the hooks of the plugin.
	 * - Hinjiwvtsp_i18n. Defines internationalization functionality.
	 * - Hinjiwvtsp_Admin. Defines all hooks for the dashboard.
	 * - Hinjiwvtsp_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-hinjiwvtsp-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-hinjiwvtsp-i18n.php';

		if ( is_admin() ) {
		
			/**
			 * The class responsible for defining all actions that occur in the Dashboard.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-hinjiwvtsp-admin.php';
			
		} else {

			/**
			 * The class responsible for defining all actions that occur in the public-facing
			 * side of the site.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-hinjiwvtsp-public.php';
			
		}

		$this->loader = new Hinjiwvtsp_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Hinjiwvtsp_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Hinjiwvtsp_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_slug() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the dashboard functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Hinjiwvtsp_Admin( array(
			'name' => $this->plugin_name, 
			'slug' => $this->plugin_slug, 
			'plugin_version' => $this->plugin_version,
			)
		);

		// load admin style sheet
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );

		// load admin javascript
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		
		// add time input fields to each widget
		$this->loader->add_action( 'in_widget_form', $plugin_admin, 'display_time_fields', 10, 3 );
		
		// sanitize user inputs and update widget options
		$this->loader->add_action( 'widget_update_callback', $plugin_admin, 'widget_update', 10, 3 );

		// add the options page and menu item
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );

		// Add an action link pointing to the options page.
		$this->loader->add_filter( 'plugin_action_links_' . WVTSP_ROOT_FILE, $plugin_admin, 'add_action_links' );

		// hook on displaying a message after plugin activation
		if ( isset( $_GET[ 'activate' ] ) or isset( $_GET[ 'activate-multi' ] ) ) {
			if ( false !== get_transient( $this->plugin_slug ) ) {
				$this->loader->add_action( 'admin_notices', $plugin_admin, 'display_activation_message' );
				delete_transient( $this->plugin_slug );
			}
		}

		// create instance of EDD Updater class
		$this->loader->add_action( 'admin_init', $plugin_admin, 'create_license_updater' );
		
		// register activation key input field
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_license_options' );
		
		// listen to $_POST and activate the license key
		$this->loader->add_action( 'admin_init', $plugin_admin, 'activate_license' );

		// listen to $_POST and deactivate the license key
		$this->loader->add_action( 'admin_init', $plugin_admin, 'deactivate_license' );
		
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Hinjiwvtsp_Public( $this->get_plugin_slug(), $this->get_version() );
		// check the visibility of each widget to display it or not
		$this->loader->add_action( 'widget_display_callback', $plugin_public, 'filter_widget' );

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
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Hinjiwvtsp_Loader    Orchestrates the hooks of the plugin.
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
		return $this->plugin_version;
	}

}
