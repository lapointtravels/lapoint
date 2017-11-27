<?php

class KMC_Destinations extends Kloon_Module {

	public $name = "Destination Boxes";
	public $type = "destination_boxes";
	public $category = "lapoint";
	public $create_new = true;
	public $fetch_existing = false;

	public function __construct () {
		add_action('init', array($this, 'create_post_type'));
		add_action("kcm/enqueue_admin_scripts", array($this, 'enqueue_admin_scripts'));

		parent::__construct();
	}

	public function create_post_type () {
		register_post_type('destinations_boxes',
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
		wp_enqueue_script('kmc_admin_destination_boxes', THEME_URL . '/kmc-modules/kmc-destinations/js/admin-destinations.js');
	}

	public function render_admin_templates () {
		include("templates/admin-destinations.php");
	}

	public function get_component_class () {
		return KMC_Destinations_Component;
	}

	public function get_new_module_icon () {
		return "<div class='select-module-box'>Destination Boxes</div>";
	}
}



class KMC_Destinations_Component extends Kloon_Component {

	public $type = "destination_boxes";

	public function __construct ($post) {
		parent::__construct($post);

		$meta_data = get_post_meta($this->post->ID);
		$this->tag = $meta_data["tag"][0];
	}

	public function update ($data, $is_revision=false) {
		parent::update($data);

		update_post_meta($this->id, 'tag', $data->tag);
	}


	public function render () {
		include("templates/destinations.php");
	}

}


new KMC_Destinations();
