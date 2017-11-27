<?php
/*
Plugin Name: Module | Info Box
Description: Add info box module to KMC
Version: 1.0.0
Author: Christian Wannerstedt @ Kloon Production AB
Author URI: http://www.kloon.se/
License: GPL
Copyright: Christian Wannerstedt
*/

function register_info_box_module() {
	include_once('kmc-info-box-class.php');
}

add_action('kcm/register_modules', 'register_info_box_module');
