<?php

class Kloon_Slideshows_Controller extends Kloon_Slides_Abstract_Controller {

	public $post_type = "kloonslideshow";
	protected $post_object = Kloon_Slides_Slideshow;

	public function __construct () {
		require_once(KLOON_SLIDES__DIR . "/models/slideshow.php");

		add_action("init", array($this, "register_post_types"));
		add_action("add_meta_boxes", array($this, "add_meta_boxes"));
		add_action("admin_enqueue_scripts", array($this, "admin_enqueue_scripts"));

		// Ajax
		add_action('wp_ajax_kloonslides_update_slideshow_settings', array($this, 'ajax_update_slideshow_settings'));
		add_action('wp_ajax_kloonslides_add_video_slide', array($this, 'ajax_add_video_slide'));
		add_action('wp_ajax_kloonslides_add_image_slide', array($this, 'ajax_add_image_slide'));
		add_action('wp_ajax_kloonslides_delete_slide', array($this, 'ajax_delete_slide'));
		add_action('wp_ajax_kloonslides_update_slide', array($this, 'ajax_update_slide'));
		add_action('wp_ajax_kloonslides_update_slide_position', array($this, 'ajax_update_slide_position'));
		add_action('wp_ajax_kloonslides_get_slideshow_json', array($this, 'ajax_get_slideshow_json'));
	}


	# ****************************** Register post types ******************************
	public function register_post_types () {
		register_post_type($this->post_type,
			array(
				"labels" => array(
					"menu_name" => __("Slides", "kloonslides"),
					"name" => __("Slideshows", "kloonslides"),
					"singular_name" => __("Slideshow", "kloonslides"),
					"add_new" => __("Add new", "kloonslides"),
					"add_new_item" => __("Add new slideshow", "kloonslides")
				),
				"public" => true,
				"has_archive" => true,
				"supports" => array("title"),
				"rewrite" => array(
					"slug" => "slides",
					"with_front" => false
				)
			)
		);
	}


	# ****************************** Helper methods ******************************
	public function get_current_slideshow () {
		// Returns a Kloon_Slideshow instance, if it's the current post.
		if (!$this->_current_post) {
			global $post;
			if ($post->post_type == $this->post_type) {
				$this->_current_post =  new Kloon_Slides_Slideshow($post);
			} else {
				$this->_current_post =  false;
			}
		}
		return $this->_current_post;
	}


	# ****************************** Meta boxes ******************************
	private function _add_meta_box ($name, $title, $function_name) {
		add_meta_box("kloonslides-" . $name, $title, array($this, $function_name), $this->post_type, "normal", "high");
	}

	public function add_meta_boxes () {
		$this->_add_meta_box("slides", "Slides", "render_slides_meta_box");
		$this->_add_meta_box("new_image", "Add new image slide", "render_new_image_meta_box");
		$this->_add_meta_box("new_video", "Add new video slide", "render_new_video_meta_box");
		$this->_add_meta_box("settings", "Settings", "render_settings_meta_box");
	}

	public function render_slides_meta_box () {
		include(KLOON_SLIDES__DIR . "/templates/slides-meta-box.php");
	}

	public function render_new_image_meta_box () {
		include(KLOON_SLIDES__DIR . "/templates/new-image-meta-box.php");
	}

	public function render_new_video_meta_box () {
		include(KLOON_SLIDES__DIR . "/templates/new-video-meta-box.php");
	}

	public function render_settings_meta_box () {
		include(KLOON_SLIDES__DIR . "/templates/settings-meta-box.php");
	}



	# ****************************** Load JS ******************************
	public function admin_enqueue_scripts ($hook) {
	    if (in_array($hook, array("post.php", "post-new.php"))) {
	        $screen = get_current_screen();

	        if (is_object($screen) && $this->post_type == $screen->post_type) {
	        	global $kloon_slides;

	        	wp_enqueue_style("kloonslides-admin-style", KLOON_SLIDES__URL . "/css/admin.css", array(), $kloon_slides->version);
	        	wp_enqueue_script("kloonslides-slideshow-show", KLOON_SLIDES__URL . "/js/admin-slideshow-show.js", array(), $kloon_slides->version);

				wp_enqueue_style("kloonslides-pnotify-style", KLOON_SLIDES__URL . "/css/pnotify.custom.min.css", array(), $kloon_slides->version);
				wp_enqueue_script("kloonslides-pnotify-script", KLOON_SLIDES__URL . "/js/pnotify.custom.min.js", array(), $kloon_slides->version);
	        }
	    }
	}


	# ****************************** Ajax ******************************
	public function ajax_update_slideshow_settings () {
		$slideshow_id = assert_numeric_post("slideshow_id");
		$slideshow = $this->get($slideshow_id);
		$slideshow->update_settings();
		json_success($slideshow);
	}

	public function ajax_add_image_slide () {
		assert_admin_access();

		$slideshow_id = assert_numeric_post("slideshow_id");
		$image_data = $_POST["data"];

		global $slides_controller;
		$slide = $slides_controller->create_image_slide($slideshow_id, $image_data);

		if ($slide) {
			json_success($slide);
		} else {
			json_error();
		}
	}

	public function ajax_add_video_slide () {
		assert_admin_access();

		$slideshow_id = assert_numeric_post("slideshow_id");

		global $slides_controller;
		$slide = $slides_controller->create_video_slide($slideshow_id, array(
			"video_type" => $_POST["video_type"],
			"video_id" => $_POST["video_id"],
			"width" => $_POST["width"],
			"height" => $_POST["height"],
		));

		if ($slide) {
			json_success($slide);
		} else {
			json_error();
		}
	}

	public function ajax_delete_slide () {
		assert_admin_access();

		$slide_id = assert_numeric_post("slide_id");
		$obj = wp_delete_post($slide_id, true);

		json_success($obj);
	}

	public function ajax_update_slide () {
		assert_admin_access();

		$slide_id = assert_numeric_post("slide_id");
		global $slides_controller;
		$slide = $slides_controller->get($slide_id);
		$slide->update();

		json_success($slide->reload());
	}

	public function ajax_update_slide_position () {
		if (isset($_POST["ids"])){
			$ids = explode(",",  $_POST["ids"]);
			$position = 0;
			foreach ($ids as $id) {
				wp_update_post(array(
					'ID' => $id,
					'menu_order' => $position
				));
				$position++;
			}

			json_success();

		} else {
			json_error();
		}
	}

	public function ajax_get_slideshow_json () {
		$slideshow_id = assert_numeric_get("slideshow_id");
		$slideshow = $this->get($slideshow_id);
		$slideshow->get_slides();

		json_success(array(
			'slideshow' => $slideshow
		));
	}

}

global $slideshows_controller;
$slideshows_controller = new Kloon_Slideshows_Controller();
