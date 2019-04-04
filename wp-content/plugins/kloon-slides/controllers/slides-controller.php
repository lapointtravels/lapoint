<?php

class Kloon_Slides_Controller extends Kloon_Slides_Abstract_Controller {

	public $post_type = "kloonslide";
	protected $post_object = "Kloon_Slides_Slide";

	public function __construct () {
		require_once(KLOON_SLIDES__DIR . "/models/slide.php");

		add_action("init", array($this, "register_post_types"));
	}


	# ****************************** Register post types ******************************
	public function register_post_types () {
		register_post_type($this->post_type,
			array(
				"labels" => array(
					"name" => __("Slides", "kloonslides"),
					"singular_name" => __("Slide", "kloonslides"),
					"add_new" => __("Add new", "kloonslides"),
					"add_new_item" => __("Add new slide", "kloonslides")
				),
				"public" => false,
				"has_archive" => false,
				"supports" => array()
			)
		);
	}

	private function _create_slide ($slideshow_id) {
		$post_id = wp_insert_post(array(
			"post_title"    => "Slide",
			"post_parent" 	=> $slideshow_id,
			"post_content"  => "-",
			"post_status"   => "publish",
			"post_type"		=> $this->post_type
		));

		if ($post_id) {
			$post = get_post($post_id);
			if ($post) {
				$slide = new Kloon_Slides_Slide($post);
				return $slide;
			}
		}

		return false;
	}

	public function create_image_slide ($slideshow_id, $image_data) {
		$slide = $this->_create_slide($slideshow_id);

		if ($slide) {
			$default_settings = apply_filters('kloonslides/image_slide_defaults', array(
				"presentation" => 1
			));

			/*
			$slide->set_data(array(
				"type" => "image",
				"image_data" => json_encode($image_data),
				"presentation" => $default_settings["presentation"]
			));
			*/
			$slide->set_data(array(
				"type" => "image",
				"image_data" => $image_data,
				"presentation" => $default_settings["presentation"]
			));

			// Avoid to double json_encoded the image data in the json response.
			// $slide->image_data = $image_data;
			return $slide->reload();
		}

		return false;
	}

	public function create_video_slide ($slideshow_id, $data) {
		$slide = $this->_create_slide($slideshow_id);

		if ($slide) {
			$default_settings = apply_filters('kloonslides/video_slide_defaults', array(
				"presentation" => 100
			));

			$data["type"] = "video";
			$data["presentation"] = $default_settings["presentation"];
			$slide->set_data($data);

			return $slide->reload();
		}

		return false;

	}

	/*public function get_slide ($id) {
		$post = get_post($id);
		return new Kloon_Slide($post);
	}*/

}

global $slides_controller;
$slides_controller = new Kloon_Slides_Controller();
