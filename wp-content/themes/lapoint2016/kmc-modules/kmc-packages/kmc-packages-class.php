<?php

class KMC_Packages extends Kloon_Module {

	public $name = "Package Boxes";
	public $type = "packages";
	public $create_new = true;
	public $fetch_existing = false;
	public $category = "lapoint";

	public function __construct () {
		add_action('init', array($this, 'create_post_type'));
		add_action("kcm/enqueue_admin_scripts", array($this, 'enqueue_admin_scripts'));

		parent::__construct();
	}

	public function create_post_type () {
		register_post_type('packages',
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
		wp_enqueue_script('kmc_admin_packages', THEME_URL . '/kmc-modules/kmc-packages/js/admin-packages.js');
	}

	public function render_admin_templates () {
		include("templates/admin-packages.php");
	}

	public function get_component_class () {
		return KMC_Packages_Component;
	}

	public function get_new_module_icon () {
		return "<div class='select-module-box'>Package Boxes</div>";
	}
}



class KMC_Packages_Component extends Kloon_Component {

	public $type = "packages";

	public function __construct ($post) {
		parent::__construct($post);
	}

	public function render () {
		include("templates/packages.php");
	}

}


new KMC_Packages();
