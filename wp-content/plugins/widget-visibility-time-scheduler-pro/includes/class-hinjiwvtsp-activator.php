<?php

/**
 * Fired during plugin activation
 *
 * @link       http://stehle-internet.de/downloads/widget-visibility-time-scheduler-pro
 * @since      1.0.0
 *
 * @package    Hinjiwvtsp
 * @subpackage Hinjiwvtsp/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Hinjiwvtsp
 * @subpackage Hinjiwvtsp/includes
 * @author     Martin Stehle <m.stehle@gmx.de>
 */
class Hinjiwvtsp_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		// store the flag into the db to trigger the display of a message after activation
		set_transient( 'hinjiwvtsp', '1', 60 );
	}

}
