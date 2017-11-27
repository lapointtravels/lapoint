<?php

class Kloon_Slide_Show {

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
