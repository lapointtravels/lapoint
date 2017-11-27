<?php
/*
Plugin Name: Module | Tabs
Description: Add tabs module to KMC
Version: 1.0.0
Author: Christian Wannerstedt @ Kloon Production AB
Author URI: http://www.kloon.se/
License: GPL
Copyright: Christian Wannerstedt
*/

function register_tabs_module() {
	include_once('kmc-tabs-class.php');
}

add_action('kcm/register_modules', 'register_tabs_module');
