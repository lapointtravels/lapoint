<?php

class KMC_Videos extends Kloon_Module {

	public $name = "Video Slider";
	public $type = "video_slider";
	public $create_new = true;
	public $fetch_existing = true;
	public $category = "lapoint";

	public function __construct () {
		add_action('init', array($this, 'create_post_type'));
		add_action("kcm/enqueue_admin_scripts", array($this, 'enqueue_admin_scripts'));

		parent::__construct();
	}

	public function create_post_type () {
		register_post_type('video_slider',
			array(
				'public' => false,
				'publicly_queryable' => false,
				'show_in_menu' => false,
				'has_archive' => false,
				'supports' => array()
			)
		);
	}

	public function enqueue_admin_scripts () {
		wp_enqueue_script('kmc_admin_videos', THEME_URL . '/kmc-modules/kmc-videos/js/admin-videos.js');
	}

	public function render_admin_templates () {
		include("templates/admin-videos.php");
	}

	public function get_component_class () {
		return KMC_Videos_Component;
	}

	public function get_new_module_icon () {
		return "<div class='select-module-box'>Video Boxes</div>";
	}

	public function action () {
		global $videos_manager;

		$action = $_POST["module_action"];
		if ($action === "get_available_videos") {

			$videos = $videos_manager->get_all();

			json_response(array(
				'status' => 200,
				'videos' => $videos
			));

		} else if ($action === "get_video") {

			$video_id = $_POST["video_id"];
			$videoObject = $videos_manager->get($video_id);
			$videoObject->thumb = wp_get_attachment_image_src(get_post_thumbnail_id($video_id), 'rect-md');
			return json_response(array(
				'status' => 200,
				'video' => $videoObject
			));

		}

		json_response(array('status' => 500));

	}
}



class KMC_Videos_Component extends Kloon_Component {

	public $type = "video_slider";

	public function __construct ($post) {
		parent::__construct($post);

		$meta_data = get_post_meta($this->post->ID);

		$videos = json_decode($meta_data["videos"][0]);

		// Fetch video data from ID
		global $videos_manager;
		$videoObjects = array();
		if ($videos) {
			foreach ($videos as $video) {
				$videoObject = $videos_manager->get($video->id);
				$videoObject->thumb = wp_get_attachment_image_src(get_post_thumbnail_id($video->id), 'rect-md');
				$videoObjects[] = $videoObject;
			}
		}
		$this->videos = $videoObjects;
	}

	public function update ($data, $is_revision=false) {
		parent::update($data);
		update_post_meta($this->post->ID, 'videos', json_encode($data->videos));
	}

	public function render () {
		include("templates/videos.php");
	}

}


new KMC_Videos();
