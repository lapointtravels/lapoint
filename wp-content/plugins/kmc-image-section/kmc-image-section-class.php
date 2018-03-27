<?php

class KMC_Image_Section extends Kloon_Module {

	public $name = "Image section";
	public $type = "image-section";
	public $create_new = true;
	public $fetch_existing = true;
	public $category = "custom";

	public function __construct () {
		add_action('init', array($this, 'create_post_type'));
		add_action("kcm/enqueue_admin_scripts", array($this, 'enqueue_admin_scripts'));

		parent::__construct();
	}

	public function create_post_type () {
		register_post_type('image-section',
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
		wp_enqueue_script('kmc_admin_image_section', plugins_url('js/admin-image-section.js', __FILE__));
		wp_enqueue_style('kmc_admin_image_section', plugins_url('css/admin-image-section.css', __FILE__));
	}

	public function render_admin_templates () {
		include("templates/admin-image-section.php");
	}

	public function get_component_class () {
		return KMC_Image_Section_Component;
	}

	public function get_new_module_icon () {
		return "<div class='select-module-box'>Image Section</div>";
	}

	public function create_instance ($data, $is_preview=false) {
		$post_id = parent::create_instance($data);
		$instance = $this->get_instance($post_id);
		$instance->update($data);
		return $post_id;
	}
}



class KMC_Image_Section_Component extends Kloon_Component {

	public $type = "image-section";

	public function __construct ($post) {
		parent::__construct($post);

		$meta_data = get_post_meta($this->post->ID);
		$this->image = json_decode($meta_data["image"][0]);
	}

	public function update ($data, $is_revision=false) {
		parent::update($data);


		// echo "Save data";
		//echo var_dump($data);

		update_post_meta($this->id, 'image', json_encode($data->image));
	}

	public function render () {
		include("templates/image-section.php");
	}

}


new KMC_Image_Section();
