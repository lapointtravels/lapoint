<?php

class Kloon_Content_Module extends Kloon_Module {

	public $create_new = true;
	public $fetch_existing = true;
	public $name = "Content";
	public $type = "kmc_content";
	public $sub_support = true;

	public function __construct () {
		add_action('init', array($this, 'create_post_type'));
		add_action("kcm/enqueue_admin_scripts", array($this, 'enqueue_admin_scripts'));

		parent::__construct();
	}

	public function create_post_type () {
		register_post_type('kmc_content',
			array(
				'labels' => array(
					'name' => __('Content', 'kmc'),
					'singular_name' => __('Content', 'kmc'),
					'add_new' => __('Add', 'kmc'),
					'add_new_item' => __('Add content', 'kmc')
				),
				'public' => true,
				'publicly_queryable' => false,
				'has_archive' => false,
				'show_in_menu' => 'kmc_admin_page',
				'supports' => array(
					'title', 'editor'
				)
			)
		);
	}

	public function enqueue_admin_scripts () {
		wp_enqueue_script('kmc_content', plugins_url('../js/admin-content.js', __FILE__));
		wp_enqueue_style('kmc_content', plugins_url('../css/admin-content.css', __FILE__));
	}

	public function get_new_module_icon () {
		return "<div class='select-module-box'>Content</div>";
	}


	public function render_admin_templates () {
		include(plugin_dir_path( __FILE__ ) . "templates/admin-content.php");
	}

	public function get_component_class () {
		return Kloon_Content_Component;
	}

	public function action () {
		$action = $_POST["module_action"];

		if ($action == "get_tinymce_editor") {
			wp_editor("", 'txt-editor-' . $_POST["editor_id"]);
			exit;
		}
	}
}


class Kloon_Content_Component extends Kloon_Component {

	public $type = "kmc_content";

	public function __construct ($post) {
		parent::__construct($post);
	}

	public function action () {
		$action = $_POST["component_action"];

		if ($action == "get_tinymce_editor") {
			wp_editor($this->post->post_content, 'txt-editor-' . $this->post->ID);
			exit;
		}
	}

	public function render () {
		include(plugin_dir_path( __FILE__ ) . "templates/content.php");
	}

}


new Kloon_Content_Module();
