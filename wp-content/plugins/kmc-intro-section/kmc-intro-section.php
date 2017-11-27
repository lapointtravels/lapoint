<?php
/*
Plugin Name: Module | Intro Section
Description: Adds intro section module to KMC
Version: 1.0.0
Author: Christian Wannerstedt @ Kloon Production AB
Author URI: http://www.kloon.se/
License: GPL
Copyright: Christian Wannerstedt
*/

function register_intro_section_module() {
	include_once('kmc-intro-section-class.php');
}

add_action('kcm/register_modules', 'register_intro_section_module');
