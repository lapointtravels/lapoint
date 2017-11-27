<?php
/*
Plugin Name: Module | Kloon Slides
Description: Add "Kloon Slides" slideshows to KMC
Version: 1.0.0
Author: Christian Wannerstedt @ Kloon Production AB
Author URI: http://www.kloon.se/
License: GPL
Copyright: Christian Wannerstedt
*/

function register_kloon_slides_module() {
	include_once('kmc-kloon-slides-class.php');
}

add_action('kcm/register_modules', 'register_kloon_slides_module');
