<?php

class KMC_Camps extends Kloon_Module {

	public $name = "Camp Boxes";
	public $type = "camps";
	public $create_new = true;
	public $fetch_existing = false;
	public $category = "lapoint";
	public $sub_support = true;

	public function __construct () {
		add_action('init', array($this, 'create_post_type'));
		add_action("kcm/enqueue_admin_scripts", array($this, 'enqueue_admin_scripts'));

		parent::__construct();
	}

	public function create_post_type () {
		register_post_type('camps',
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
		wp_enqueue_script('kmc_admin_camps', THEME_URL . '/kmc-modules/kmc-camps/js/admin-camps.js');
	}

	public function render_admin_templates () {
		include("templates/admin-camps.php");
	}

	public function get_component_class () {
		return KMC_Camps_Component;
	}

	public function get_new_module_icon () {
		return "<div class='select-module-box'>Camp Boxes</div>";
	}
}



class KMC_Camps_Component extends Kloon_Component {

	public $type = "camps";

	public function __construct ($post) {
		parent::__construct($post);
	}

	public function render () {
		include("templates/camps.php");
	}

}


new KMC_Camps();
