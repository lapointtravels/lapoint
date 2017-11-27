<?php
/*
Plugin Name: Module | Quote Section
Description: Adds qoute section module to KMC
Version: 1.0.0
Author: Christian Wannerstedt @ Kloon Production AB
Author URI: http://www.kloon.se/
License: GPL
Copyright: Christian Wannerstedt
*/

function register_quote_section_module() {
	include_once('kmc-quote-section-class.php');
}

add_action('kcm/register_modules', 'register_quote_section_module');
