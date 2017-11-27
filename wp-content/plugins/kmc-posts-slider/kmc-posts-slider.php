<?php
/*
Plugin Name: Module | Posts Slider
Description: Add post slider module to KMC
Version: 1.0.0
Author: Christian Wannerstedt @ Kloon Production AB
Author URI: http://www.kloon.se/
License: GPL
Copyright: Christian Wannerstedt
*/

function register_posts_slider_module() {
	include_once('kmc-posts-slider-class.php');
}

add_action('kcm/register_modules', 'register_posts_slider_module');
