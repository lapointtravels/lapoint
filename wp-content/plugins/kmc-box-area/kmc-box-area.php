<?php
/*
Plugin Name: Module | Box Area
Description: Add box area module to KMC
Version: 1.0.0
Author: Christian Wannerstedt @ Kloon Production AB
Author URI: http://www.kloon.se/
License: GPL
Copyright: Christian Wannerstedt
*/

function register_box_area_module() {
	include_once('kmc-box-area-class.php');
}

add_action('kcm/register_modules', 'register_box_area_module');
