<?php

class KMC_Posts_Slider extends Kloon_Module {

	public $name = "Posts Slider";
	public $type = "posts-slider";
	public $create_new = true;
	public $fetch_existing = true;
	public $category = "custom";

	public function __construct () {
		add_action('init', array($this, 'create_post_type'));
		add_action("kcm/enqueue_admin_scripts", array($this, 'enqueue_admin_scripts'));

		parent::__construct();
	}

	public function create_post_type () {
		register_post_type('posts-slider',
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
		wp_enqueue_script('kmc_admin_posts_slider', plugins_url('js/admin-posts-slider.js', __FILE__));
		wp_enqueue_style('kmc_admin_posts_slider', plugins_url('css/admin-posts-slider.css', __FILE__));
	}

	public function render_admin_templates () {
		include("templates/admin-posts-slider.php");
	}

	public function get_component_class () {
		return "KMC_Posts_Slider_Component";
	}

	public function get_new_module_icon () {
		return "<div class='select-module-box'>Posts slider</div>";
	}
}



class KMC_Posts_Slider_Component extends Kloon_Component {

	public $type = "posts-slider";

	public function __construct ($post) {
		parent::__construct($post);
	}

	public function extra_classes () {
		return "posts-slider";
	}

	public function render () {
		wp_enqueue_style('kmc_posts_slider', plugins_url('css/posts-slider.css', __FILE__));

		include("templates/posts-slider.php");
	}

}

new KMC_Posts_Slider();