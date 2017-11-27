<?php
/**
 * @package Kloon Slides
 * @version 0.9.14
 */
/*
Plugin Name: Kloon Slides
Description: Create slideshows with images and videos
Author: Christian Wannerstedt @ Kloon Production AB
Version: 0.9.14
Author URI: http://www.kloon.se
*/


define("KLOON_SLIDES__DIR", dirname(__FILE__));
define("KLOON_SLIDES__URL", plugins_url("", __FILE__));

require_once(KLOON_SLIDES__DIR .'/lib/utils.php');

class Kloon_Slides {

	public $version = "0.9.14";

	# ****************************** Cunstructor ******************************
	public function __construct () {

		require_once("models/abstract.php");
		require_once("controllers/abstract-controller.php");
		require_once("controllers/slideshows-controller.php");
		require_once("controllers/slides-controller.php");

		add_shortcode('slideshow', array($this, 'slideshow_shortcode'));
	}


	public function slideshow_shortcode ($attributes) {
		if (isset($attributes["id"])) {
			$id = $attributes["id"];

			global $slideshows_controller;
			$slideshow = $slideshows_controller->get($id);
			return $slideshow->get_output_html();
		}
		return "*** Invalid shortcode ***";
	}

	public function render ($slideshow_id) {
		global $slideshows_controller;
		$slideshow = $slideshows_controller->get($slideshow_id);
		return $slideshow->get_output_html();
	}

	public function get_settings () {
		if (!$this->settings) {
			$this->settings = apply_filters('kloonslides/settings', array(
				"media_library_sizes" => array(
					"lg" => array(
						"name" => "image-2500",
						"width" => 2500
					),
					"md" => array(
						"name" => "image-1200",
						"width" => 1200
					),
					"sm" => array(
						"name" => "image-770",
						"width" => 770
					)
				),

				"image_presentations" => array(
					1 => array(
						"id" => 1,
						"label" => "Image only",
						"class" => "presentation-1",
						"fields" => array()
					),
					2 => array(
						"id" => 2,
						"label" => "Title",
						"class" => "presentation-2",
						"fields" => array(
							array(
								"label" => "Title",
								"key" => "title",
								"type" => "textfield",
								"tag" => "h2"
							),
							array(
								"label" => "Align",
								"key" => "align",
								"type" => "select",
								"append_class" => true,
								"options" => array(
									"left" => "Left",
									"center" => "Center",
									"right" => "Right"
								)
							)
						)
					),
					3 => array(
						"id" => 3,
						"label" => "Title + info + CTA",
						"class" => "presentation-3",
						"fields" => array(
							array(
								"label" => "Title",
								"key" => "title",
								"type" => "textfield",
								"tag" => "h2"
							),
							array(
								"label" => "Info",
								"key" => "info",
								"type" => "textfield"
							),
							array(
								"label" => "CTA",
								"key" => "cta",
								"type" => "link",
								"classes" => "btn btn-cta btn-primary"
							),
							array(
								"label" => "Align",
								"key" => "align",
								"type" => "select",
								"append_class" => true,
								"options" => array(
									"left" => "Left",
									"center" => "Center",
									"right" => "Right"
								)
							)
						)
					)
				),

				"video_presentations" => array(
					100 => array(
						"id" => 100,
						"label" => "Video only",
						"class" => "presentation-100",
						"fields" => array(),
					),
					101 => array(
						"id" => 101,
						"label" => "Preview",
						"class" => "presentation-101",
						"fields" => array(
							array(
								"label" => "Title",
								"key" => "title",
								"type" => "textfield",
								"tag" => "h2"
							),
							array(
								"label" => "Info",
								"key" => "info",
								"type" => "textfield"
							),
							array(
								"label" => "CTA",
								"key" => "cta",
								"type" => "link",
								"classes" => "btn btn-cta btn-primary"
							)
						),
						"settings" => array(
							"has_bgr_video" => true
						)
					)
				)
			));
		}

		return $this->settings;
	}


	public function get_all_slideshows () {
		global $slideshows_controller;
		return $slideshows_controller->get_all();
	}

	/*
	public function get_image_presentation_object ($presentation_id) {
		$settings = $this->get_settings();
		if ($settings["image_presentations"][$presentation_id]) {
			return $settings["image_presentations"][$presentation_id];
		} else {
			return false;
		}
	}

	public function get_video_presentation_object ($presentation_id) {
		$settings = $this->get_settings();
		if ($settings["video_presentations"][$presentation_id]) {
			return $settings["video_presentations"][$presentation_id];
		} else {
			return false;
		}
	}
	*/

	/*public function setup_admin_menu () {
		# Add the menu button
		add_menu_page('Manage slide shows', __('Slides'), 'manage_options', 'kloonslides-index', array($this, 'render_index_view'), 'dashicons-format-gallery', 3);

		add_submenu_page('kloonslides-index', 'New slide show', 'New slide show', 'manage_options', 'kloonslides-new', array($this, 'render_new_view'));

		add_submenu_page('kloonslides-edit-slide-show', 'Edit slide show', 'Edit slide show', 'manage_options', 'kloonslides-edit-slide-show', array($this, 'render_edit_view'));
	}*/

}

global $kloon_slides;
$kloon_slides = new Kloon_Slides();
