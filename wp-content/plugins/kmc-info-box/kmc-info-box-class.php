<?php

class KMC_Info_Box extends Kloon_Module {

	public $name = "Info Box";
	public $type = "info-box";
	public $create_new = true;
	public $fetch_existing = true;
	public $category = "custom";

	public function __construct () {
		add_action('init', array($this, 'create_post_type'));
		add_action("kcm/enqueue_admin_scripts", array($this, 'enqueue_admin_scripts'));

		parent::__construct();
	}

	public function create_post_type () {
		register_post_type('info-box',
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
		wp_enqueue_script('kmc_admin_info_box', plugins_url('js/admin-info-box.js', __FILE__));
		wp_enqueue_style('kmc_admin_info_box', plugins_url('css/admin-info-box.css', __FILE__));
	}

	public function render_admin_templates () {
		include("templates/admin-info-box.php");
	}

	public function get_component_class () {
		return KMC_Info_Box_Component;
	}

	public function get_new_module_icon () {
		return "<div class='select-module-box'>Info Box</div>";
	}

	public function create_instance ($data) {
		$post_id = parent::create_instance($data);
		$instance = $this->get_instance($post_id);
		$instance->update($data);
		return $post_id;
	}
}



class KMC_Info_Box_Component extends Kloon_Component {

	public $type = "info-box";

	public function __construct ($post) {
		parent::__construct($post);

		$meta_data = get_post_meta($this->post->ID);
		$this->icon = $meta_data["icon"][0];
		$this->button_text = $meta_data["button_text"][0];
		$this->button_link = $meta_data["button_link"][0];
	}

	public function extra_classes () {
		return "info-box";
	}

	public function update ($data) {
		parent::update($data);

		update_post_meta($this->id, 'icon', $data->icon);
		update_post_meta($this->id, 'button_text', $data->button_text);
		update_post_meta($this->id, 'button_link', $data->button_link);
	}


	public function render () {
		include("templates/info-box.php");
	}

}


new KMC_Info_Box();
