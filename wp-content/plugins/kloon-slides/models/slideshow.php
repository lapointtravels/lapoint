<?php

class Kloon_Slides_Slideshow extends Kloon_Slides_Abstract_Model {

	public function __construct ($post) {
		parent::__construct($post);

		$this->title = $post->post_title;

		$meta = get_post_meta($this->id);
		$default_settings = apply_filters('kloonslides/slideshow_defaults', array(
			"size" => "fixed",
			"timer" => 0,
			"fixed_height_px" => 550,
			"hide_nav" => false
		));

		if (!$meta["size"]) {
			$this->size = $default_settings["size"];
		} else {
			$this->size = $meta["size"][0];
		}

		if (!$meta["timer"]) {
			$this->timer = $default_settings["timer"];
		} else {
			$this->timer = $meta["timer"][0];
		}

		if (!$meta["fixed_height_px"]) {
			$this->fixed_height_px = $default_settings["fixed_height_px"];
		} else {
			$this->fixed_height_px = $meta["fixed_height_px"][0];
		}

		if (!$meta["fixed_height_tablet"]) {
			$this->fixed_height_tablet = $default_settings["fixed_height_tablet"];
		} else {
			$this->fixed_height_tablet = $meta["fixed_height_tablet"][0];
		}

		if (!$meta["fixed_height_phone"]) {
			$this->fixed_height_phone = $default_settings["fixed_height_phone"];
		} else {
			$this->fixed_height_phone = $meta["fixed_height_phone"][0];
		}

		if (!$meta["hide_nav"]) {
			$this->hide_nav = $default_settings["hide_nav"];
		} else {
			$this->hide_nav = ($meta["hide_nav"][0] == "true");
		}

		$this->is_fixed_height = ($this->size == "fixed");
		$this->is_fullscreen = ($this->size == "fullscreen");
		$this->is_dynamic_size = ($this->size == "dynamic");
	}

	public function update_settings () {
		$timer = $_POST["timer"];
		$size = $_POST["size"];
		$fixed_height_px = $_POST["fixed_height_px"];
		$fixed_height_tablet = $_POST["fixed_height_tablet"];
		$fixed_height_phone = $_POST["fixed_height_phone"];
		$hide_nav = $_POST["hide_nav"];

		update_post_meta($this->id, 'timer', $timer);
		update_post_meta($this->id, 'size', $size);
		update_post_meta($this->id, 'fixed_height_px', $fixed_height_px);
		update_post_meta($this->id, 'fixed_height_tablet', $fixed_height_tablet);
		update_post_meta($this->id, 'fixed_height_phone', $fixed_height_phone);
		update_post_meta($this->id, 'hide_nav', $hide_nav);

		if ($size !== $this->size) {
			// 	$this->regenerate_slides();
		}

		// Update current instance
		$this->timer = $timer;
		$this->size = $size;
	}

	public function get_slides () {
		if (!$this->_slides) {
			global $slides_controller;

			$slides = get_posts(array(
				'post_parent' => $this->id,
				'post_type'   => $slides_controller->post_type,
				'numberposts' => -1,
				'post_status' => 'publish',
				'orderby' => 'menu_order',
				'order' => 'ASC'
			));

			if ($slides) {
				$this->_slides = array_map(function ($slide) {
					return new Kloon_Slides_Slide($slide);
				}, $slides);
			} else {
				$this->_slides = [];
			}
		}

		return $this->_slides;
	}

	public function get_output_html () {
		// Added necessary js and css files
		wp_enqueue_script(array(
			'jquery',
			'jquery-effects-core'
		));

		global $kloon_slides;
		$version = $kloon_slides->version;
		wp_enqueue_script('kloonslides-detect-swipe', KLOON_SLIDES__URL . "/js/jquery.detect_swipe.js", array(), $version);
		wp_enqueue_script('kloonslides-slideshow', KLOON_SLIDES__URL . "/js/kloonslides-slideshow.js", array(), $version);
		wp_enqueue_style('kloonslides-style', KLOON_SLIDES__URL . "/css/kloonslides.css", array(), $version);
		wp_enqueue_style('kloonslides-theme', KLOON_SLIDES__URL . "/css/theme-1.css", array(), $version);

		// Construct output
		ob_start();
		include_once(KLOON_SLIDES__DIR . "/templates/slideshow-shared.php");
		include(KLOON_SLIDES__DIR . "/templates/slideshow.php");
		$slideshow_html = ob_get_contents();
		ob_end_clean();
		return $slideshow_html;
	}
}

class TestTestTest {

	public function __construct ($data) {
		// The constructor accepts both ID or post object
		if (is_numeric($data)) {
			$post = get_post($data);
			$this->init_with_post($post);

		} else if (is_object($data)) {
			$this->init_with_post($data);
		}
	}

	private function init_with_post ($post) {
		//echo "Init with post<br>";
		$this->id = $post->ID;
		$this->title = $post->post_title;

		// Needed by KMC Imageslider
		$this->ID = $post->ID;
		$this->post_title = $post->post_title;

		$this->_meta = get_post_meta($this->id);

		$this->size = $this->_meta["size"][0];
		$this->timer = $this->_meta["timer"][0];
	}

	public function get_slides () {
		//echo "<br>Get slides";
		if (!$this->_slides) {
			global $wpdb;
			$posts = $wpdb->get_results(sprintf(
				"SELECT * FROM `%s` WHERE slide_show_id='%d' ORDER BY position;",
				Kloon_Image_Slider::get_db_slides_table(),
				$this->id
			));

			$this->_slides = array_map(function ($post) {
				return new Kloon_Slide($post);
			}, $posts);
		}

		return $this->_slides;
	}

	public function is_fullscreen () {
		return ($this->size === "fullscreen");
	}

	public function is_fixed_height () {
		return ($this->size === "fixed");
	}

	public function is_dynamic_height () {
		return ($this->size === "dynamic");
	}


	# ****************************** Path & Urls ******************************
	public function get_upload_root_url () {
		if (!$this->_root_url) {
			global $kloon_image_slider;
			$settings = $kloon_image_slider->get_settings();

			$this->_root_url = $settings["upload_dir_url"];

			if ($settings["use_seperate_upload_folders"]) {
				$this->_root_url .= $this->id . "/";
			}
		}

		return $this->_root_url;
	}
	public function get_upload_root_path () {
		if (!$this->_root_path) {
			global $kloon_image_slider;
			$settings = $kloon_image_slider->get_settings();

			$this->_root_path = $settings["upload_dir_path"];

			if ($settings["use_seperate_upload_folders"]) {
				$this->_root_path .= "/" . $this->id;
			}
		}

		return $this->_root_path;
	}


	# ****************************** Update ******************************
	public function update () {
		$title = $_POST["title"];
		$timer = $_POST["timer"];
		$size = $_POST["size"];

		wp_update_post(array(
			'ID' => $this->id,
			'post_title' => $title
		));

		// update_post_meta($slide_show_id, 'transition_time', intval($_POST['transition_time']));
		// update_post_meta($slide_show_id, 'easing', $_POST['easing']);
		update_post_meta($this->id, 'timer', $timer);

		// Update current instance
		$this->title = $title;
		$this->timer = $timer;

		if ($size !== $this->size) {
			update_post_meta($this->id, 'size', $size);
			$this->size = $size;
			$this->regenerate_slides();
		}
	}


	# ****************************** Delete ******************************
	public function delete () {
		$slides = $this->get_slides();
		foreach ($slides as $slide){
			$slide->delete_images(False);
		}

		// Delete db records
		global $wpdb, $kloon_image_slider;
		$wpdb->get_results(sprintf(
			"DELETE FROM `%s` WHERE slide_show_id='%d';",
			Kloon_Image_Slider::get_db_slides_table(),
			$this->id
		));

		wp_delete_post($this->id, true);

		// Remove dir
		if ($kloon_image_slider->get_settings()["use_seperate_upload_folders"]) {
			if (file_exists($this->get_upload_root_path())) {
				rmdir($this->get_upload_root_path());
			}
		}
	}


	# ****************************** Private Misc ******************************
	private function regenerate_slides () {
		foreach ($this->get_slides() as $slide) {
			$slide->regenerate_images($this->size);
		}
	}

}
