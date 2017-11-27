<?php
/*
Plugin Name: Module | Alertbar
Description: Adds alertbar module to KMC
Version: 1.0.0
Author: Christian Wannerstedt @ Kloon Production AB
Author URI: http://www.kloon.se/
License: GPL
Copyright: Christian Wannerstedt
*/

function register_alertbar_module() {
	include_once('kmc-alertbar-class.php');
}

add_action('kcm/register_modules', 'register_alertbar_module');
