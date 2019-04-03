<?php

class KMC_Intro_Section extends Kloon_Module {

	public $name = "Intro Section";
	public $type = "intro-section";
	public $create_new = true;
	public $fetch_existing = true;
	public $category = "custom";

	public function __construct () {
		add_action('init', array($this, 'create_post_type'));
		add_action("kcm/enqueue_admin_scripts", array($this, 'enqueue_admin_scripts'));

		parent::__construct();
	}

	public function create_post_type () {
		register_post_type('intro-section',
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
		wp_enqueue_script('kmc_admin_intro_section', plugins_url('js/admin-intro-section.js', __FILE__));
		wp_enqueue_style('kmc_admin_intro_section', plugins_url('css/admin-intro-section.css', __FILE__));
	}

	public function render_admin_templates () {
		include("templates/admin-intro-section.php");
	}

	public function get_component_class () {
		return "KMC_Intro_Section_Component";
	}

	public function get_new_module_icon () {
		return "<div class='select-module-box'>Intro Section</div>";
	}

	public function create_instance ($data, $is_preview=false) {
		$post_id = parent::create_instance($data);
		$instance = $this->get_instance($post_id);
		$instance->update($data);
		return $post_id;
	}
}



class KMC_Intro_Section_Component extends Kloon_Component {

	public $type = "intro-section";

	public function __construct ($post) {
		parent::__construct($post);

		$meta_data = get_post_meta($this->post->ID);
		$this->col1_title = $meta_data["col1_title"][0];
		$this->col1_content = $meta_data["col1_content"][0];
		$this->col2_title = $meta_data["col2_title"][0];
		$this->col2_content = $meta_data["col2_content"][0];
	}

	public function extra_classes () {
		return "intro-section";
	}

	public function update ($data, $is_revision=false) {
		parent::update($data);

		update_post_meta($this->id, 'col1_title', $data->col1_title);
		update_post_meta($this->id, 'col1_content', $data->col1_content);
		update_post_meta($this->id, 'col2_title', $data->col2_title);
		update_post_meta($this->id, 'col2_content', $data->col2_content);
	}


	public function render () {
		include("templates/intro-section.php");
	}

}


new KMC_Intro_Section();
