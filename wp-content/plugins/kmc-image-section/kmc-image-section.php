<?php
/*
Plugin Name: Module | Image Section
Description: Adds image section module to KMC
Version: 1.0.0
Author: Christian Wannerstedt @ Kloon Production AB
Author URI: http://www.kloon.se/
License: GPL
Copyright: Christian Wannerstedt
*/

function register_image_section_module() {
	include_once('kmc-image-section-class.php');
}

add_action('kcm/register_modules', 'register_image_section_module');
