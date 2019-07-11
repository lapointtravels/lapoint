<?php

class KMC_Booking_Bar extends Kloon_Module {

	public $name = "Booking bar";
	public $type = "booking-bar";
	public $category = "lapoint";
	public $create_new = true;

	public function __construct () {
		add_action('init', array($this, 'create_post_type'));
		add_action("kcm/enqueue_admin_scripts", array($this, 'enqueue_admin_scripts'));

		parent::__construct();
	}

	public function create_post_type () {
		register_post_type('booking-bar',
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
		wp_enqueue_script('kmc_booking_bar', THEME_URL . '/kmc-modules/kmc-booking-bar/js/admin-booking-bar.js', array(), 7);
	}

	public function render_admin_templates () {
		include("templates/admin-booking-bar.php");
	}

	public function get_component_class () {
		return "KMC_Booking_Bar_Component";
	}

	public function get_new_module_icon () {
		return "<div class='select-module-box'>Booking bar</div>";
	}

	public function create_instance ($data, $is_preview=false) {
		$post_id = parent::create_instance($data);
		$instance = $this->get_instance($post_id);
		$instance->update($data);
		return $post_id;
	}
}



class KMC_Booking_Bar_Component extends Kloon_Component {

	public $type = "booking-bar";

	public function __construct ($post) {
		parent::__construct($post);

		$meta_data = get_post_meta($this->post->ID);
		$this->default_destination_type = $meta_data["default_destination_type"][0];
		$this->default_destination = $meta_data["default_destination"][0];
		$this->default_camp = $meta_data["default_camp"][0];
		$this->default_level = $meta_data["default_level"][0];
		$this->auto_search = $meta_data["auto_search"][0] == "1";
		$this->show_description = $meta_data["show_description"] ? $meta_data["show_description"][0] : 1 ;
	}

	public function update ($data, $is_revision=false) {
		parent::update($data);

		update_post_meta($this->id, 'default_destination_type', $data->default_destination_type);
		update_post_meta($this->id, 'default_destination', $data->default_destination);
		update_post_meta($this->id, 'default_camp', $data->default_camp);
		update_post_meta($this->id, 'default_level', $data->default_level);
		update_post_meta($this->id, 'auto_search', $data->auto_search);
		update_post_meta($this->id, 'show_description', $data->show_description);
	}

	public function render () {
		include("templates/booking-bar.php");
	}

}


new KMC_Booking_Bar();
