<?php

class KMC_Alertbar extends Kloon_Module {

	public $name = "Alertbar";
	public $type = "alertbar";
	public $create_new = true;
	public $fetch_existing = true;
	public $category = "custom";

	public function __construct () {
		add_action('init', array($this, 'create_post_type'));
		add_action("kcm/enqueue_admin_scripts", array($this, 'enqueue_admin_scripts'));

		parent::__construct();
	}

	public function create_post_type () {
		register_post_type('alertbar',
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
		wp_enqueue_script('kmc_admin_alertbar', plugins_url('js/admin-alertbar.js', __FILE__));
		wp_enqueue_style('kmc_admin_alertbar', plugins_url('css/admin-alertbar.css', __FILE__));
	}

	public function render_admin_templates () {
		include("templates/admin-alertbar.php");
	}

	public function get_component_class () {
		return KMC_Alertbar_Component;
	}

	public function get_new_module_icon () {
		return "<div class='select-module-box'>Alertbar</div>";
	}

	public function create_instance ($data) {
		$post_id = parent::create_instance($data);
		$instance = $this->get_instance($post_id);
		$instance->update($data);
		return $post_id;
	}
}



class KMC_Alertbar_Component extends Kloon_Component {

	public $type = "alertbar";

	public function __construct ($post) {
		parent::__construct($post);

		$meta_data = get_post_meta($this->post->ID);
		$this->button_text = $meta_data["button_text"][0];
		$this->button_link = $meta_data["button_link"][0];
	}

	public function extra_classes () {
		return "alertbar";
	}

	public function update ($data) {
		parent::update($data);

		update_post_meta($this->id, 'button_text', $data->button_text);
		update_post_meta($this->id, 'button_link', $data->button_link);
	}


	public function render () {
		include("templates/alertbar.php");
	}

}


new KMC_Alertbar();
