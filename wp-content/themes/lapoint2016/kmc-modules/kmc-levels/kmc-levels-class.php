<?php

class KMC_Levels extends Kloon_Module {

	public $name = "Level Boxes";
	public $type = "levels";
	public $create_new = true;
	public $fetch_existing = true;
	public $category = "lapoint";

	public function __construct () {
		add_action('init', array($this, 'create_post_type'));
		add_action("kcm/enqueue_admin_scripts", array($this, 'enqueue_admin_scripts'));

		parent::__construct();
	}

	public function create_post_type () {
		register_post_type('levels',
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
		wp_enqueue_script('kmc_admin_levels', THEME_URL . '/kmc-modules/kmc-levels/js/admin-levels.js');
	}

	public function render_admin_templates () {
		include("templates/admin-levels.php");
	}

	public function get_component_class () {
		return KMC_Levels_Component;
	}

	public function get_new_module_icon () {
		return "<div class='select-module-box'>Level Boxes</div>";
	}
}



class KMC_Levels_Component extends Kloon_Component {

	public $type = "levels";

	public function __construct ($post) {
		parent::__construct($post);
	}

	public function extra_classes () {
		return "levels";
	}

	public function render () {
		include("templates/levels.php");
	}

}


new KMC_Levels();
