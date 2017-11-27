<?php
/*
Plugin Name: Module | Imageslider
Description: Add imageslider to KMC
Version: 1.0.0
Author: Christian Wannerstedt @ Kloon Production AB
Author URI: http://www.kloon.se/
License: GPL
Copyright: Christian Wannerstedt
*/

function register_imageslider_module() {
	include_once('kmc-imageslider-class.php');
}

add_action('kcm/register_modules', 'register_imageslider_module');
