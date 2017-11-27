<?php

class KMC_Header extends Kloon_Module {

	public $name = "Generic header";
	public $type = "header";
	public $create_new = true;
	public $fetch_existing = true;
	public $category = "lapoint";

	public function __construct () {
		add_action('init', array($this, 'create_post_type'));
		add_action("kcm/enqueue_admin_scripts", array($this, 'enqueue_admin_scripts'));

		parent::__construct();
	}

	public function create_post_type () {
		register_post_type('header',
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
		wp_enqueue_script('kmc-headers', THEME_URL . '/kmc-modules/kmc-header/js/admin-header.js');
	}

	public function render_admin_templates () {
		include("templates/admin-header.php");
	}

	public function get_component_class () {
		return KMC_Header_Component;
	}

	public function get_new_module_icon () {
		return "<div class='select-module-box'>Generic Header</div>";
	}
}



class KMC_Header_Component extends Kloon_Component {

	public $type = "headers";

	public function __construct ($post) {
		parent::__construct($post);
	}

	public function render () {
		include("templates/header.php");
	}

}


new KMC_Header();
