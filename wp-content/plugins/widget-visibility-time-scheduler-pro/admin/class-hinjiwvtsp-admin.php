<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       http://stehle-internet.de/downloads/widget-visibility-time-scheduler-pro
 * @since      1.0.0
 *
 * @package    Hinjiwvtsp
 * @subpackage Hinjiwvtsp/admin
 * @author     Martin Stehle <m.stehle@gmx.de>
 */

class Hinjiwvtsp_Admin {

	/**
	 * The slug of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_slug    The slug of this plugin.
	 */
	private $plugin_slug;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_version    The current version of this plugin.
	 */
	private $plugin_version;

	/**
	 * Plugin author
	 *
	 * @since    3.0.0
	 * @access   private
	 * @var     string
	 */
	private $plugin_author;

	/**
	 * Name of action for license activation
	 *
	 * @since    3.0.0
	 * @access   private
	 * @var      string
	 */
	private $license_activation_action_name;

	/**
	 * Name of action for license deactivation
	 *
	 *
	 * @since    3.0.0
	 * @access   private
	 * @var      string
	 */
	private $license_deactivation_action_name;

	/**
	 * Unique identifier in the WP options table
	 *
	 * @since    3.0.0
	 * @access   private
	 * @var      string
	 */
	private $license_key_option_name;

	/**
	 * Slug of license status option in the DB
	 *
	 * @since    3.0.0
	 * @access   private
	 * @var      array
	 */
	private $license_status_option_name;
	
	/**
	 * Group name of options
	 *
	 * @since    3.0.0
	 * @access   private
	 * @var      array
	 */
	private $license_settings_fields_slug;

	/**
	 * Slug of the license activation page
	 *
	 * @since    3.0.0
	 * @access   private
	 * @var      array
	 */
	private $license_page_slug;

	/**
	 * Slug of nonce
	 *
	 * @since    3.0.0
	 * @access   private
	 * @var      string
	 */
	private $nonce_field_name;
	
	/**
	 * This is the URL the Easy Digital Downloads updater / license checker pings
	 * This should be the URL of the site with EDD installed
	 *
	 * @since    3.0.0
	 * @access   private
	 * @var      string
	 *
	 */
	private $plugin_shop_url;

	/**
	 * Actions of widget
	 *
	 * @since    4.0.0
	 * @access   private
	 * @var      array    $modes    actions of widget
	 */
	private $modes;

	/**
	 * Current day on server
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $current_dd    current day
	 */
	private $current_dd;

	/**
	 * Current month on server
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $current_mm    current month
	 */
	private $current_mm;

	/**
	 * Current year on server
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $current_yy    current year
	 */
	private $current_yy;

	/**
	 * Current hour on server
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $current_hh    current hour
	 */
	private $current_hh;

	/**
	 * Current minute on server
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $current_mn    current minute
	 */
	private $current_mn;

	/**
	 * Current second on server
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $current_ss    current second
	 */
	private $current_ss;

	/**
	 * Start and end names for widget
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $names_widget_boundaries    Start and end names for widget
	 */
	private $names_widget_boundaries;

	/**
	 * Values and names for the weekdays
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $names_widget_boundaries    Values and names for the weekdays
	 */
	private $weekdays;

	/**
	 * Form field ids
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $field_ids   distinct ids of form field elements
	 */
	private $field_ids;

	/**
	 * Current widget time settings
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $scheduler   scheduler settings for the current widget
	 */
	private $scheduler;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $plugin_slug       The name of this plugin.
	 * @var      string    $plugin_version    The version of this plugin.
	 */
	public function __construct( $args ) {

		$this->plugin_name						= $args['name'];
		$this->plugin_slug						= $args['slug'];
		$this->plugin_version					= $args['plugin_version'];
		$this->nonce_field_name					= 'wvtsp_edd_nonce';
		$this->license_key_option_name			= 'widget-visibility-time-scheduler-pro-license-key';
		$this->license_status_option_name		= 'widget-visibility-time-scheduler-pro-license-status';
		$this->license_settings_fields_slug		= 'wvtsp_edd_setting_fields';
		$this->license_deactivation_action_name	= 'wvtsp_edd_license_deactivate';
		$this->license_activation_action_name	= 'wvtsp_edd_license_activate';
		$this->license_page_slug				= 'wvtsp-licence-page';
		$this->plugin_author					= 'Martin Stehle';
		$this->plugin_shop_url					= 'https://shop.stehle-internet.de';
		$this->modes = array( 'Show', 'Hide' );
		$this->names_widget_boundaries = array( 'widget-start', 'widget-end' );
		$this->weekdays = array(
			1 => array(
				'name' => 'Monday',
				'abbr' => 'mon'
			),
			2 => array(
				'name' => 'Tuesday',
				'abbr' => 'tue'
			),
			3 => array(
				'name' => 'Wednesday',
				'abbr' => 'wed'
			),
			4 => array(
				'name' => 'Thursday',
				'abbr' => 'thu'
			),
			5 => array(
				'name' => 'Friday',
				'abbr' => 'fri'
			),
			6 => array(
				'name' => 'Saturday',
				'abbr' => 'sat'
			),
			7 => array(
				'name' => 'Sunday',
				'abbr' => 'sun'
			)
		);

		// set current date and time vars
		$timestamp = current_time( 'timestamp' ); // get current local blog timestamp
		$this->current_yy = idate( 'Y', $timestamp ); // get year as integer, 4 digits
		$this->current_mm = idate( 'm', $timestamp ); // get month number as integer
		$this->current_dd = idate( 'd', $timestamp ); // get day number as integer
		$this->current_hh = idate( 'H', $timestamp ); // get hour as integer, 24 hour format
		$this->current_mn = idate( 'i', $timestamp ); // get minute as integer
		$this->current_ss = 0; // set seconds to zero
		
		// not in use, just for the po-editor to display the translation on the plugins overview list
		$foo = __( 'Control the visibility of each widget based on date, time and weekday easily.', 'hinjiwvtsp' );

	}

	/**
	 * Register the stylesheets for the Dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles( $hook_suffix ) {
		// load only if we are on the Widgets page
		if ( 'widgets.php' == $hook_suffix ) {
			if( is_rtl() ) {
				wp_enqueue_style( $this->plugin_slug, plugin_dir_url( __FILE__ ) . 'css/hinjiwvtsp-admin-rtl.css', array(), $this->plugin_version, 'all' );
			} else {
				wp_enqueue_style( $this->plugin_slug, plugin_dir_url( __FILE__ ) . 'css/hinjiwvtsp-admin.css', array(), $this->plugin_version, 'all' );
			}
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    5.0
	 */
	public function enqueue_scripts( $hook_suffix ) {

		// load only if we are on the Widgets page
		if ( 'widgets.php' == $hook_suffix ) {
			// scripts for the admin pages
			wp_enqueue_script( $this->plugin_slug, plugin_dir_url( __FILE__ ) . 'js/hinjiwvtsp-admin.js', array( 'jquery' ), $this->plugin_version, false );

			// translations in scripts
			$translations = array(
				'open_scheduler' => __( 'Open scheduler', 'hinjiwvtsp' ),
				'close_scheduler' => __( 'Close scheduler', 'hinjiwvtsp' ),
			);
			wp_localize_script( $this->plugin_slug, 'wvtsp_i18n', $translations );

		}
		
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function add_plugin_admin_menu() {
		
		// Add 'License Activation' sub page
		add_options_page(
			sprintf( __( 'License Activation for %s', 'hinjiwvtsp' ), $this->plugin_name ), // page title
			sprintf( __( '%s License', 'hinjiwvtsp' ), $this->plugin_name ), // menu title
			'manage_options', // capatibility
			$this->license_page_slug, // menu slug for this page
			array( $this, 'print_license_page' ) // callable function
		);

	}

	/**
	 * Add action link to the plugins page
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function add_action_links( $links ) {

	$url = sprintf( 'admin.php?page=%s', $this->license_page_slug );
		return array_merge(
			$links,
			array(
				'activation' => sprintf( '<a href="%s">%s</a>', admin_url( $url ), esc_html__( 'License Activation', 'hinjiwvtsp' ) )
			)
		);

	}

	/**
	 * Print a message about the location of the plugin in the WP backend
	 * 
	 * @since    1.0.0
	 */
	public function display_activation_message () {
		
		$text_1 = 'Appearance';
		$text_2 = 'Widgets';
		$text_3 = 'Settings';
		
		if ( is_rtl() ) {
			$sep = '&lsaquo;';
			// set link #1
			$link_1 = sprintf(
				'<a href="%s">%s %s %s</a>',
				esc_url( admin_url( 'widgets.php' ) ),
				esc_html__( $text_2 ),
				$sep,
				esc_html__( $text_1 )
			);

			// set link #2
			$link_2 = sprintf(
				'<a href="%s">%s %s %s</a>',
				esc_url( admin_url( sprintf( 'admin.php?page=%s', $this->license_page_slug ) ) ),
				esc_html__( 'License Activation', 'hinjiwvtsp' ),
				$sep,
				esc_html__( $text_3 )
			);
		} else {
			$sep = '&rsaquo;';
			// set link #1
			$link_1 = sprintf(
				'<a href="%s">%s %s %s</a>',
				esc_url( admin_url( 'widgets.php' ) ),
				esc_html__( $text_1 ),
				$sep,
				esc_html__( $text_2 )
			);

			// set link #2
			$link_2 = sprintf(
				'<a href="%s">%s %s %s</a>',
				esc_url( admin_url( sprintf( 'admin.php?page=%s', $this->license_page_slug ) ) ),
				esc_html__( $text_3 ),
				$sep,
				esc_html__( 'License Activation', 'hinjiwvtsp' )
			);
		}
		
		// set whole message
		printf(
			'<div class="updated"><p>%s %s</p></div>',
			sprintf( 
				esc_html__( 'Welcome to Widget Visibility Time Scheduler Pro! You can set the time based visibility in each widget on the page %s.', 'hinjiwvtsp' ),
				$link_1
			),
			sprintf( 
				esc_html__( 'To manage the license go to the page %s.', 'hinjiwvtsp' ),
				$link_2
			)
		);

	}

	/**
	 * Add the widget conditions to each widget in the admin.
	 *
	 * @param $widget unused.
	 * @param $return unused.
	 * @param array $widget_settings The widget settings.
	 */
	public function display_time_fields( $widget, $return, $widget_settings ) {
		
		$this->field_ids = array();
		$this->scheduler = array();

		// prepare html elements ids for widget start and end time
		foreach( array( 'yy', 'mm', 'dd', 'hh', 'mn', 'ss' ) as $field_name ) {
			foreach( $this->names_widget_boundaries as $boundary ) {
				$name = $boundary . '-' . $field_name;
				$this->field_ids[ $name ] = $widget->get_field_id( $name );
			}
		}

		// prepare html elements ids for weekdays' start and end times
		foreach( $this->weekdays as $i => $values ) {
			foreach( array( 'start', 'end' ) as $boundary ) {
				foreach( array( 'hh', 'mn', 'ss' ) as $field_name ) {
					$name = $values[ 'abbr' ] . '-' . $boundary . '-' . $field_name; // e.g. 'mon-start-hh'
					$this->field_ids[ $name ] = $widget->get_field_id( $name );
				}
			}
		}

		// check and sanitize stored settings; if not set: set them to current time
		if ( isset( $widget_settings[ $this->plugin_slug ] ) ) {
			$this->scheduler = $widget_settings[ $this->plugin_slug ];
		}

		/* deprecated since v2.0:
		// scheduler status
		if ( isset( $this->scheduler[ 'is_active' ] ) ) {
			$this->scheduler[ 'is_active' ] = true;
		} else {
			$this->scheduler[ 'is_active' ] = false;
		}
		*/

		/* deprecated since v2.0:
		// infinite end
		if ( isset( $this->scheduler[ 'widget-end-infinite' ] ) ) {
			$this->scheduler[ 'widget-end-infinite' ] = true;
		} else {
			$this->scheduler[ 'widget-end-infinite' ] = false;
		}
		*/
		
		// modes
		if ( isset( $this->scheduler[ 'mode' ] ) and in_array( $this->scheduler[ 'mode' ], $this->modes ) ) {
			// pass
		} else {
			$this->scheduler[ 'mode' ] = '';
		}
		// convert from plugin version < 4.0
		if ( isset( $this->scheduler[ 'is_active' ] ) ) {
			$this->scheduler[ 'mode' ] = $this->modes[ 0 ]; // mode = show
			unset( $this->scheduler[ 'is_active' ] );
			if ( isset( $this->scheduler[ 'is_opposite' ] ) ) {
				$this->scheduler[ 'mode' ] = $this->modes[ 1 ]; // mode = hide
				unset( $this->scheduler[ 'is_opposite' ] );
			}
		}

		// start and end times
		if ( isset( $this->scheduler[ 'timestamps' ] ) ) {
			foreach( $this->names_widget_boundaries as $boundary ) {
				if ( isset( $this->scheduler[ 'timestamps' ][ $boundary ] ) ) {
					$timestamp = (int) $this->scheduler[ 'timestamps' ][ $boundary ]; // get stored Unix timestamp
				} else {
					$timestamp = current_time( 'timestamp' ); // get current local blog timestamp
				}
				$this->scheduler[ $boundary . '-yy' ] = idate( 'Y', $timestamp ); // get year as integer, 4 digits
				$this->scheduler[ $boundary . '-mm' ] = idate( 'm', $timestamp ); // get month number as integer
				$this->scheduler[ $boundary . '-dd' ] = idate( 'd', $timestamp ); // get day number as integer
				$this->scheduler[ $boundary . '-hh' ] = idate( 'H', $timestamp ); // get hour as integer, 24 hour format
				$this->scheduler[ $boundary . '-mn' ] = idate( 'i', $timestamp ); // get minute as integer
			}
			foreach( $this->weekdays as $i => $values ) {
				foreach ( array( 'start', 'end' ) as $boundary ) {
					$key = $values[ 'abbr' ] . '-' . $boundary;
					if ( isset( $this->scheduler[ 'timestamps' ][ $key ] ) ) {
						$timestamp = (int) $this->scheduler[ 'timestamps' ][ $key ]; // get stored mini timestamp
					} else {
						if ( 'start' == $boundary ) {
							$hh = 0;
							$mn = 0;
						} else {
							$hh = 23;
							$mn = 59;
						}
						$timestamp = mktime( $hh, $mn, 0, 1, 1, 1970 ); // get default mini timestamp
					}
					$this->scheduler[ $key . '-hh' ] = idate( 'H', $timestamp ); // get hour as integer, 24 hour format
					$this->scheduler[ $key . '-mn' ] = idate( 'i', $timestamp ); // get minute as integer
				}
			}
		} else {
			$timestamp = current_time( 'timestamp' ); // get current local blog timestamp
			foreach( $this->names_widget_boundaries as $boundary ) {
				$this->scheduler[ $boundary . '-yy' ] = idate( 'Y', $timestamp ); // get year as integer, 4 digits
				$this->scheduler[ $boundary . '-mm' ] = idate( 'm', $timestamp ); // get month number as integer
				$this->scheduler[ $boundary . '-dd' ] = idate( 'd', $timestamp ); // get day number as integer
				$this->scheduler[ $boundary . '-hh' ] = idate( 'H', $timestamp ); // get hour as integer, 24 hour format
				$this->scheduler[ $boundary . '-mn' ] = idate( 'i', $timestamp ); // get minute as integer
			}
			foreach( $this->weekdays as $i => $values ) {
				$this->scheduler[ $values[ 'abbr' ] . '-' . 'start-hh' ] = 0;
				$this->scheduler[ $values[ 'abbr' ] . '-' . 'start-mn' ] = 0;
				$this->scheduler[ $values[ 'abbr' ] . '-' . 'end-hh' ] = 23;
				$this->scheduler[ $values[ 'abbr' ] . '-' . 'end-mn' ] = 59;
			}
		}
		
		// weekdays
		if ( isset( $this->scheduler[ 'daysofweek' ] ) ) {
			$sanitized_daysofweek = array_map( 'absint', $this->scheduler[ 'daysofweek' ] ); // convert values from string to positive integers
			foreach ( range( 1, 7 ) as $dayofweek ) {
				if ( in_array( $dayofweek, $sanitized_daysofweek ) ) {
					$this->scheduler[ 'daysofweek' ][] = $dayofweek;
				}
			}
		} else {
			// default: all checked
			$this->scheduler[ 'daysofweek' ] = range( 1, 7 );
		}

		// print additional input fields in widget
		include 'partials/fieldsets.php';
		
		// return null because new fields are added
		return null;
	}

	/**
	 * Print out HTML form date elements for editing widget publish date.
	 *
	 * Borrowed from WP-own function touch_current_time( 'timestamp' ) in /wp-admin/includes/template.php
	 *
	 * @since 1.0.0
	 *
	 * @param string $boundary
	 */
	private function touch_time( $boundary ) {
		global $wp_locale;
		
		// check and sanitize stored settings

		//  month
		$label = 'Month';
		$name = $boundary . '-mm';
		$var = isset( $this->scheduler[ $name ] ) ? absint( $this->scheduler[ $name ] ) : $this->current_mm;
		$values[ $name ] = ( 1 <= $var and $var <= 12 ) ? zeroise( $var, 2 ) : zeroise( $this->current_mm, 2 );
		$month = sprintf(
			'<label for="%s" class="screen-reader-text">%s</label><select id="%s" name="%s[%s]">',
			$this->field_ids[ $name ],
			__( $label ),
			$this->field_ids[ $name ],
			$this->plugin_slug,
			$name 
		);
		$label = '%1$s-%2$s';
		for ( $i = 1; $i < 13; $i = $i +1 ) {
			$monthnum = zeroise($i, 2); // add leading zero for values < 10
			$month .= sprintf(
				'<option value="%s" %s>',
				$monthnum,
				selected( $monthnum, $values[ $name ], false ) 
			);
			/* translators: 1: month number (01, 02, etc.), 2: month abbreviation */
			$month .= sprintf( __( $label ), $monthnum, $wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) ) ) . '</option>';
		}
		$month .= '</select>';

		//  year
		$label = 'Year';
		$name = $boundary . '-yy';
		$var = isset( $this->scheduler[ $name ] ) ? absint( $this->scheduler[ $name ] ) : $this->current_yy;
		$values[ $name ] = ( 1970 <= $var and $var <= 2037 ) ? strval( $var ) : zeroise( $this->current_yy, 2 );
		$year   = sprintf(
			'<label for="%s" class="screen-reader-text">%s</label><input type="text" id="%s" name="%s[%s]" value="%s" size="4" maxlength="4" autocomplete="off" />',
			$this->field_ids[ $name ],
			__( $label ),
			$this->field_ids[ $name ],
			$this->plugin_slug,
			$name,
			$values[ $name ] 
		);

		//  day
		$label = 'Day';
		$name = $boundary . '-dd';
		$var = isset( $this->scheduler[ $name ] ) ? absint( $this->scheduler[ $name ] ) : $this->current_dd;
		$values[ $name ] = ( 1 <= $var and $var <= 31 ) ? zeroise( $var, 2 ) : zeroise( $this->current_dd, 2 );
		$day = sprintf(
			'<label for="%s" class="screen-reader-text">%s</label><input type="text" id="%s" name="%s[%s]" value="%s" size="2" maxlength="2" autocomplete="off" />',
			$this->field_ids[ $name ],
			__( $label ),
			$this->field_ids[ $name ],
			$this->plugin_slug,
			$name,
			$values[ $name ] 
		);

		//  hour
		$label = 'Hour';
		$name = $boundary . '-hh';
		$var = isset( $this->scheduler[ $name ] ) ? absint( $this->scheduler[ $name ] ) : $this->current_hh;
		$values[ $name ] = ( 0 <= $var and $var <= 23 ) ? zeroise( $var, 2 ) : zeroise( $this->current_hh, 2 );
		$hour = sprintf(
			'<label for="%s" class="screen-reader-text">%s</label><input type="text" id="%s" name="%s[%s]" value="%s" size="2" maxlength="2" autocomplete="off" />',
			$this->field_ids[ $name ],
			__( $label ),
			$this->field_ids[ $name ],
			$this->plugin_slug,
			$name,
			$values[ $name ] 
		);

		//  minute
		$label = 'Minute';
		$name = $boundary . '-mn';
		$var = isset( $this->scheduler[ $name ] ) ? absint( $this->scheduler[ $name ] ) : $this->current_mn;
		$values[ $name ] = ( 0 <= $var and $var <= 59 ) ? zeroise( $var, 2 ) : zeroise( $this->current_mn, 2 );
		$minute = sprintf(
			'<label for="%s" class="screen-reader-text">%s</label><input type="text" id="%s" name="%s[%s]" value="%s" size="2" maxlength="2" autocomplete="off" />',
			$this->field_ids[ $name ],
			__( $label ),
			$this->field_ids[ $name ],
			$this->plugin_slug,
			$name,
			$values[ $name ] 
		);

		/* translators: 1: month, 2: day, 3: year, 4: hour, 5: minute */
		$label = '%1$s %2$s, %3$s @ %4$s:%5$s';
		printf( __( $label ), $month, $day, $year, $hour, $minute ) . "\n";

		//  seconds
		$name = $boundary . '-ss';
		printf(
			'<input type="hidden" id="%s" name="%s[%s]" value="00" maxlength="2" />',
			$this->field_ids[ $name ],
			$this->plugin_slug,
			$name 
		) . "\n";

	}
	
	/**
	 * Print out HTML form date elements for editing weekdays publish time.
	 *
	 * Borrowed from WP-own function touch_current_time( 'timestamp' ) in /wp-admin/includes/template.php
	 *
	 * @since 1.0.0
	 *
	 * @param string $boundary
	 */
	private function touch_weekdays_time( $boundary, $weekday_abbr, $weekday_name ) {
	
		if ( 'start' == $boundary ) {
			$text_boundary =  __( 'from', 'hinjiwvtsp' );
			$default_hh = 0;
			$default_mn = 0;
		} else {
			$text_boundary =  __( 'to', 'hinjiwvtsp' );
			$default_hh = 23;
			$default_mn = 59;
		}

		// check and sanitize stored settings

		//  hour
		$text = 'Hour';
		$text = __( $text ); // translated to current language
		$label = sprintf( '%s %s %s', $weekday_name, $text_boundary, $text );
		$name = $weekday_abbr . '-' . $boundary . '-hh';
		$var = isset( $this->scheduler[ $name ] ) ? absint( $this->scheduler[ $name ] ) : $default_hh;
		$value = ( 0 <= $var and $var <= 23 ) ? zeroise( $var, 2 ) : zeroise( $default_hh, 2 );
		$hour = sprintf( 
			'<label for="%s" class="screen-reader-text">%s</label><input type="text" id="%s" name="%s[%s]" value="%s" size="2" maxlength="2" autocomplete="off" />',
			$this->field_ids[ $name ],
			$label,
			$this->field_ids[ $name ],
			$this->plugin_slug,
			$name,
			$value 
		);

		//  minute
		$text = 'Minute';
		$text = __( $text ); // translated to current language
		$label = sprintf( '%s %s %s', $weekday_name, $text_boundary, $text );
		$name = $weekday_abbr . '-' . $boundary . '-mn';
		$var = isset( $this->scheduler[ $name ] ) ? absint( $this->scheduler[ $name ] ) : $default_mn;
		$value = ( 0 <= $var and $var <= 59 ) ? zeroise( $var, 2 ) : zeroise( $default_mn, 2 );
		$minute = sprintf( 
			'<label for="%s" class="screen-reader-text">%s</label><input type="text" id="%s" name="%s[%s]" value="%s" size="2" maxlength="2" autocomplete="off" />',
			$this->field_ids[ $name ],
			$label,
			$this->field_ids[ $name ],
			$this->plugin_slug,
			$name,
			$value 
		);

		printf( '%s : %s', $hour, $minute ) . "\n";

		//  seconds
		$name = $weekday_abbr . '-' . $boundary . '-ss';
		printf(
			'<input type="hidden" id="%s" name="%s[%s]" value="00" maxlength="2" />',
			$this->field_ids[ $name ],
			$this->plugin_slug,
			$name 
		) . "\n";

	}
	
	/**
	 * On an AJAX update of the widget settings, sanitize and return the display conditions.
	 *
	 * @param	array	$new_widget_settings	New settings for this instance as input by the user.
	 * @param	array	$old_widget_settings	Old settings for this instance.
	 * @return	array	$widget_settings		Processed settings.
	 */
	public static function widget_update( $widget_settings, $new_widget_settings, $old_widget_settings ) {
		
		$datetime = array();
		$scheduler = array();
		$plugin_slug = 'hinjiwvtsp';
		
		// sanitize user input

		// if neither activated nor weekday checked, save time and quit now without settings
		/* deprecated since v4.0:
		if ( ! isset( $_POST[ $plugin_slug ][ 'is_active' ] ) or ! isset( $_POST[ $plugin_slug ][ 'daysofweek' ] ) ) {
		*/
		if ( empty( $_POST[ $plugin_slug ][ 'mode' ] ) ) {
			// if former settings are in the widget_settings: delete them
			if ( isset( $widget_settings[ $plugin_slug ] ) ) {
				unset( $widget_settings[ $plugin_slug ] );
			}
			return $widget_settings;
		}
		
		// get weekdays values
		$sanitized_daysofweek = array_map( 'absint', $_POST[ $plugin_slug ][ 'daysofweek' ] ); // convert values from string to positive integers
		$scheduler[ 'daysofweek' ] = array();
		foreach ( range( 1, 7 ) as $dayofweek ) {
			if ( in_array( $dayofweek, $sanitized_daysofweek ) ) {
				$scheduler[ 'daysofweek' ][] = $dayofweek;
			}
		}
		// if no valid weekday given, save time and quit now without settings
		if ( empty( $scheduler[ 'daysofweek' ]) ) {
			if ( isset( $widget_settings[ $plugin_slug ] ) ) {
				unset( $widget_settings[ $plugin_slug ] );
			}
			return $widget_settings;
		}

		/* deprecated since v4.0:
		// set active status
		$scheduler[ 'is_active' ] = 1;
		*/
		
		// set widget action: show / hide ?
		/* deprecated since v4.0:
		if ( isset( $_POST[ $plugin_slug ][ 'is_opposite' ] ) ) {
			$scheduler[ 'is_opposite' ] = 1;
		}
		*/
		if ( isset( $_POST[ $plugin_slug ][ 'mode' ] ) and in_array( $_POST[ $plugin_slug ][ 'mode' ], array( 'Show', 'Hide' ) ) ) {
			$scheduler[ 'mode' ] = $_POST[ $plugin_slug ][ 'mode' ];
		}

		/* deprecated since v4.0:
		// if neither activated nor weekday checked, save time and quit now without settings
		if ( isset( $_POST[ $plugin_slug ][ 'widget-end-infinite' ] ) ) {
			$scheduler[ 'widget-end-infinite' ] = true;
		}
		*/

		// set current date and time vars
		// (neccessary to write it once more instead of re-use $this->xx because we are here in a non-object context)
		$timestamp = current_time( 'timestamp' ); // get current local blog timestamp
		$current_yy = idate( 'Y', $timestamp ); // get year as integer, 4 digits
		$current_mm = idate( 'm', $timestamp ); // get month number as integer
		$current_dd = idate( 'd', $timestamp ); // get day number as integer
		$current_hh = idate( 'H', $timestamp ); // get hour as integer, 24 hour format
		$current_mn = idate( 'i', $timestamp ); // get minute as integer
		$current_ss = 0; // set seconds to zero

		// set timestamps of widget start and end
		foreach( array( 'widget-start', 'widget-end' ) as $boundary ) {
			// year
			$name = $boundary . '-yy';
			$var = isset( $_POST[ $plugin_slug ][ $name ] ) ? absint( $_POST[ $plugin_slug ][ $name ] ) : $current_yy;
			$datetime[ $name ] = ( 1970 <= $var and $var <= 2037 ) ? $var : $current_yy;
			// month
			$name = $boundary . '-mm';
			$var = isset( $_POST[ $plugin_slug ][ $name ] ) ? absint( $_POST[ $plugin_slug ][ $name ] ) : $current_mm;
			$datetime[ $name ] = ( 1 <= $var and $var <= 12 ) ? $var : $current_mm;
			// day
			$name = $boundary . '-dd';
			$var = isset( $_POST[ $plugin_slug ][ $name ] ) ? absint( $_POST[ $plugin_slug ][ $name ] ) : $current_dd;
			$datetime[ $name ] = ( 1 <= $var and $var <= 31 ) ? $var : $current_dd;
			// hour
			$name = $boundary . '-hh';
			$var = isset( $_POST[ $plugin_slug ][ $name ] ) ? absint( $_POST[ $plugin_slug ][ $name ] ) : $current_hh;
			$datetime[ $name ] = ( 0 <= $var and $var <= 23 ) ? $var : $current_hh;
			// minute
			$name = $boundary . '-mn';
			$var = isset( $_POST[ $plugin_slug ][ $name ] ) ? absint( $_POST[ $plugin_slug ][ $name ] ) : $current_mn;
			$datetime[ $name ] = ( 0 <= $var and $var <= 59 ) ? $var : $current_mn;
			// second
			$name = $boundary . '-ss';
			$datetime[ $name ] = 0;
			
			$scheduler[ 'timestamps' ][ $boundary ] = mktime(
				$datetime[ $boundary . '-hh' ],
				$datetime[ $boundary . '-mn' ],
				$datetime[ $boundary . '-ss' ],
				$datetime[ $boundary . '-mm' ],
				$datetime[ $boundary . '-dd' ],
				$datetime[ $boundary . '-yy' ]
			);
		}

		// set timestamps of weekdays' starts and ends
		foreach( array( 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun' ) as $month ) {
			foreach( array( 'start', 'end' ) as $boundary ) {
				$key = $month . '-' . $boundary;
				
				if ( 'start' == $boundary ) {
					$default_hh = 0;
					$default_mn = 0;
				} else {
					$default_hh = 23;
					$default_mn = 59;
				}

				// hour
				$name = $key . '-hh';
				$var = isset( $_POST[ $plugin_slug ][ $name ] ) ? absint( $_POST[ $plugin_slug ][ $name ] ) : $default_hh;
				$time[ $name ] = ( 0 <= $var and $var <= 23 ) ? $var : $default_hh;
				// minute
				$name = $key . '-mn';
				$var = isset( $_POST[ $plugin_slug ][ $name ] ) ? absint( $_POST[ $plugin_slug ][ $name ] ) : $default_mn;
				$time[ $name ] = ( 0 <= $var and $var <= 59 ) ? $var : $default_mn;
				// second
				$name = $key . '-ss';
				$time[ $name ] = 0;
				
				$scheduler[ 'timestamps' ][ $key ] = mktime(
					$time[ $key . '-hh' ],
					$time[ $key . '-mn' ],
					$time[ $key . '-ss' ],
					1,
					1,
					1970
				);
			}
		}

		// if too high year set the highest possible values for the end date and time
		$name = 'widget-end-yy';
		$var = isset( $_POST[ $plugin_slug ][ $name ] ) ? absint( $_POST[ $plugin_slug ][ $name ] ) : $current_yy;
		if ( 2037 < $var ) {
			$scheduler[ 'timestamps' ][ 'widget-end' ] = mktime( 23, 59, 59, 12, 31, 2037 );
			// store the flag into the db to trigger the display of a message after activation
			set_transient( $plugin_slug, '1', 60 );
		}

		// return sanitized user settings
		$widget_settings[ $plugin_slug ] = $scheduler;
		return $widget_settings;
	}

	/* ============================
	 * Methods for license managing
	 * ============================ */

	/**
	 *
	 * Creates an instance of EDD Updater class
	 *
	 * @access   public
	 * @since    1.0.0
	 */
	public function create_license_updater() {

		// retrieve our license key from the DB
		$license_key = trim( get_option( $this->license_key_option_name ) );

		// load EDD custom updater if not available
		if( ! class_exists( 'Stehle_EDD_SL_Plugin_Updater' ) ) {
			include( dirname( __FILE__ ) . '/includes/EDD_SL_Plugin_Updater.php' );
		}

		// setup the updater
		$edd_updater = new Stehle_EDD_SL_Plugin_Updater(
			$this->plugin_shop_url,
			dirname( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'hinjiwvtsp.php',
			array(
				'version' 	=> $this->plugin_version, // current plugin_version number
				'license' 	=> $license_key, 		// license key (used get_option above to retrieve from DB)
				'item_name' => $this->plugin_name, 	// name of this plugin
				'author' 	=> $this->plugin_author  // author of this plugin
			)
		);
	}

	/**
	 *
	 * Register activation key input field
	 *
	 * @access   public
	 * @since    1.0.0
	 */
	public function register_license_options() {
		// creates our settings in the options table
		register_setting(
			// group name in settings_fields() ?
			$this->license_settings_fields_slug,
			// name of the option to sanitize and save in the db
			$this->license_key_option_name,
			// callback function that sanitizes the option's values
			array( $this, 'sanitize_license_options' )
		);
	}

	/**
	 * Check and return correct values for the settings
	 *
	 * @access   public
	 * @since    1.0.0
	 * @param   array    $input    Options and their values after submitting the form
	 * @return  array              Options and their sanatized values
	 */
	public function sanitize_license_options ( $new ) {
		$old = get_option( $this->license_key_option_name );
		if( $old && $old != $new ) {
			delete_option( $this->license_status_option_name ); // new license has been entered, so must reactivate
		}
		return $new;
	}
	
	/**
	 * Activate license key if desired
	 *
	 * @access   public
	 * @since    1.0.0
	 */
	public function activate_license() {

		// listen for our activate button to be clicked
		if( isset( $_POST[ $this->license_activation_action_name ] ) ) {

			// run a quick security check
			if( ! check_admin_referer( $this->license_activation_action_name, $this->nonce_field_name ) ) {
				return; // get out if we didn't click the Activate button
			}

			// retrieve the license from the database
			$license = trim( get_option( $this->license_key_option_name ) );

			// data to send in our API request
			$api_params = array(
				'edd_action'=> 'activate_license',
				'license' 	=> $license,
				'item_name' => urlencode( $this->plugin_name ), // the name of our product in EDD
				'url'       => home_url()
			);
			
			// Call the custom API.
			$response = wp_remote_post(
				$this->plugin_shop_url,
				array(
					'timeout' => 15,
					'sslverify' => false,
					'body' => $api_params 
				)
			);

			// make sure the response came back okay
			if ( $this->license_has_error( $response ) ) {
				// quit
				return false;
			}

			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			
			if ( false === $license_data->success ) {
				// store message for later use in license admin page
				set_transient( 'hinjiwvtsp_message', $this->get_license_message_by_status( $license_data->error ) );
			} // if (license_data not success)
			
			// store license validity
			update_option( $this->license_status_option_name, $license_data->license );

		} // if( isset( $_POST[ $this->license_activation_action_name ] ) )
	}

	/**
	 * Dectivate license key if desired
	 *
	 * @access   public
	 * @since    1.0.0
	 */
	public function deactivate_license() {

		// listen for our activate button to be clicked
		if( isset( $_POST[ $this->license_deactivation_action_name ] ) ) {

			// run a quick security check
			if( ! check_admin_referer( $this->license_deactivation_action_name, $this->nonce_field_name ) ) {
				return; // get out if we didn't click the Activate button
			}

			// retrieve the license from the database
			$license = trim( get_option( $this->license_key_option_name ) );

			// data to send in our API request
			$api_params = array(
				'edd_action'=> 'deactivate_license',
				'license' 	=> $license,
				'item_name' => urlencode( $this->plugin_name ), // the name of our product in EDD
				'url'       => home_url()
			);
			
			// Call the custom API.
			$response = wp_remote_post(
				$this->plugin_shop_url,
				array(
					'timeout' => 15,
					'sslverify' => false,
					'body' => $api_params 
				)
			);

			// make sure the response came back okay
			if ( $this->license_has_error( $response ) ) {
				// quit
				return false;
			}

			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			
			// $license_data->license will be either "deactivated" or "failed"
			if( $license_data->license == 'deactivated' ) {
				delete_option( $this->license_status_option_name );
			}
			
		} // if( isset( $_POST[ $this->license_deactivation_action_name ] ) )
	}

	/**
	 * Check and return correct values for the settings
	 *
	 * @access   private
	 * @since    1.0.0
	 * @return  array              Options and their sanatized values
	 */
	private function check_license() {

		global $wp_version;

		$api_params = array(
			'edd_action' => 'check_license',
			'license'    => trim( get_option( $this->license_key_option_name ) ),
			'item_name'  => urlencode( $this->plugin_name ),
			'url'        => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post(
			// url
			$this->plugin_shop_url,
			// arguments	
			array(
				'timeout' => 15,
				'sslverify' => false,
				'body' => $api_params 
			)
		);

		// make sure the response came back okay
		if ( $this->license_has_error( $response ) ) {
			// quit
			return false;
		}

		// return license informations
		return json_decode( wp_remote_retrieve_body( $response ) );
		
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @access   public
	 * @since    1.0.0
	 */
	public function print_license_page() {
		
		// get stored license data
		$license_key 	  = get_option( $this->license_key_option_name );
		$license_validity = get_option( $this->license_status_option_name );

		// Get existing message if available
		if ( false === ( $msg = get_transient( 'hinjiwvtsp_message' ) ) ) {
			// initialize vars
			$timestamp = false;
			$activations_left = 0;

			// check license status
			$license_data = $this->check_license();
			
			// set license vars
			if ( false === $license_data ) {
				$license_status = $license_validity; // or "invalid" ?
			} else {
				if ( isset( $license_data->expires ) ) {
					$timestamp = strtotime( $license_data->expires ); // returns false if ->expires is false, too
				}
				if ( isset( $license_data->activations_left ) ) {
					if ( 'unlimited' == $license_data->activations_left ) {
						$activations_left = 9999;
					} else {
						$activations_left = (int) $license_data->activations_left;
					}
				}
				$license_status = $license_data->license;
			};

			// get message based on status
			$msg = $this->get_license_message_by_status( $license_status );
		} else {
			$license_status = 'error';
			delete_transient( 'hinjiwvtsp_message' );
		}
		
		// print page
		include_once( 'partials/page-license.php' );
	}
	
	/**
	 * Parse response for error; store error message if available
	 *
	 * @access   private
	 * @since    4.0
	 */
	private function license_has_error ( $response ) {
		$message = '';
		if ( is_wp_error( $response ) ) {
			$message = $response->get_error_message();
		} elseif ( isset( $response['response']['code'] ) and 200 !== $response['response']['code'] ) {
			$text = 'An unknown error occurred';
			$message = sprintf( '%s (%d)', __( $text ), $response['response']['code'] );
		}
		if ( $message ) {
			// store message for later use in license admin page
			set_transient( 'hinjiwvtsp_message', $message );
			// quit: has error
			return true;
		} else {
			// quit: no error
			return false;
		}
	}
	
	/**
	 * Return a readable message for the license status
	 *
	 * @access   private
	 * @since    4.0
	 */
	private function get_license_message_by_status ( $status ) {
		switch ( $status ) {
			case 'disabled':
				return __( 'The license is disabled.', 'hinjiwvtsp' );
			case 'expired':
				return __( 'The license is expired.', 'hinjiwvtsp' );
			case 'inactive':
				return __( 'The license is inactive.', 'hinjiwvtsp' );
			case 'invalid':
				return __( 'The license is invalid.', 'hinjiwvtsp' );
			case 'invalid_item_id':
				return __( 'This appears to be an invalid product for this license key.', 'hinjiwvtsp' );
			case 'key_mismatch':
				return __( 'The license key does not match.', 'hinjiwvtsp' );
			case 'item_name_mismatch':
				return __( 'This appears to be an invalid product name for this license key.', 'hinjiwvtsp' );
			case 'license_not_activable':
				return __( 'The license is not activatable.', 'hinjiwvtsp' );
			case 'missing':
				return __( 'The license key does not exist.', 'hinjiwvtsp' );
			case 'no_activations_left':
				return __( 'The license has reached its activation limit.', 'hinjiwvtsp' );
			case 'revoked':
				return __( 'The license has been revoked.', 'hinjiwvtsp' );
			case 'site_inactive':
				return sprintf( __( 'The license is not active for the site address %s.', 'hinjiwvtsp' ), home_url() );
			case 'valid':
				return __( 'The license is valid and active.', 'hinjiwvtsp' );
			default:
				return sprintf( __( 'The license status is: %s.', 'hinjiwvtsp' ), $status );
		} // switch ( status )
	}

}
