<?php

class KMC_Locations extends Kloon_Module {

	public $name = "Location Boxes";
	public $type = "locations";
	public $create_new = true;
	public $fetch_existing = false;
	public $category = "lapoint";

	public function __construct () {
		add_action('init', array($this, 'create_post_type'));
		add_action("kcm/enqueue_admin_scripts", array($this, 'enqueue_admin_scripts'));

		parent::__construct();
	}

	public function create_post_type () {
		register_post_type('locations',
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
		wp_enqueue_script('kmc_admin_locations', THEME_URL . '/kmc-modules/kmc-locations/js/admin-locations.js');
	}

	public function render_admin_templates () {
		include("templates/admin-locations.php");
	}

	public function get_component_class () {
		return "KMC_Locations_Component";
	}

	public function get_new_module_icon () {
		return "<div class='select-module-box'>Location Boxes</div>";
	}


	public function create_instance ($data, $is_preview=false) {
		$post_id = parent::create_instance($data);
		$instance = $this->get_instance($post_id);
		$instance->update($data);
		return $post_id;
	}
}



class KMC_Locations_Component extends Kloon_Component {

	public $type = "locations";

	public function __construct ($post) {
		parent::__construct($post);

		$meta_data = get_post_meta($this->post->ID);
		$this->location = $meta_data["location"][0];
	}

	public function update ($data, $is_revision=false) {
		parent::update($data);

		update_post_meta($this->id, 'location', $data->location);
	}

	public function render () {
		include("templates/locations.php");
	}

}


new KMC_Locations();
