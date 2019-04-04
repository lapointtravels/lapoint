<?php

class KMC_Tabs extends Kloon_Module {

	public $name = "Tabs";
	public $type = "tabs";
	public $create_new = true;
	public $fetch_existing = true;
	public $has_admin_page = true;

	public function __construct () {
		add_action('init', array($this, 'create_post_type'));
		add_action("kcm/enqueue_admin_scripts", array($this, 'enqueue_admin_scripts'));

		parent::__construct();
	}

	public function create_post_type () {
		register_post_type('tabs',
			array(
				'labels' => array(
					'name' => __('Tabs', 'kmc'),
					'singular_name' => __('Tab', 'kmc'),
					'add_new' => __('Add', 'kmc'),
					'add_new_item' => __('Add tab', 'kmc')
				),
    			'show_in_menu' => 'kmc_admin_page',
				'public' => true,
				'publicly_queryable' => false,
				'has_archive' => false,
				'supports' => array('title')
			)
		);
	}

	public function enqueue_admin_scripts () {
		wp_enqueue_script('kmc_tabs_admin_script', plugins_url('js/admin-tabs.js', __FILE__));
		//wp_enqueue_script('jquery.responsiveTabs', plugins_url('js/jquery.responsiveTabs.js', __FILE__));
		wp_enqueue_style('kmc_tabs_admin_style', plugins_url('css/admin-tabs.css', __FILE__));
	}

	public function render_admin_templates () {
		include("templates/admin-tabs.php");
	}

	public function get_component_class () {
		return "KMC_Tabs_Component";
	}

	public function get_new_module_icon () {
		return "<div class='select-module-box'>Tabs</div>";
	}

	public function create_instance ($data, $is_preview=false) {
		$post_id = parent::create_instance($data);
		$instance = $this->get_instance($post_id);
		$instance->update($data);
		return $post_id;
	}
}




class KMC_Tabs_Component extends Kloon_Component {

	public $type = "tabs";

	public function __construct ($post) {
		parent::__construct($post);


		global $module_controller;

		$label = get_post_meta($this->id, 'label', true);
		$this->label = $label;

		$tabs = get_post_meta($this->id, 'tabs', true);
		$fetched_tabs = array();
		if ($tabs) {
			$tabs_json = json_decode($tabs);
			foreach ($tabs_json as $tab_json) {
				$tab = (object) array(
					"components" => array(),
					"title" => $tab_json->title,
					"label" => $tab_json->label
				);
				foreach ($tab_json->components as $post_id) {
					$component = $module_controller->get_component($post_id);
					if ($component) {
						$tab->components[] = $component;
					}
				}
				$fetched_tabs[] = $tab;
			}
		}
		$this->tabs = $fetched_tabs;
	}


	public function update ($data, $is_revision=false) {
		parent::update($data);
		//update_post_meta($this->post->ID, 'tabs_boxes', json_encode($boxes));
		$log = false;


		update_post_meta($this->id, 'label', $data->label);

		if ($log) echo "<hr><br>";
		if ($log) echo var_dump($data);
		if ($log) echo "<hr><br>";


		$tabs = $data->tabs;
		//$tabs =  json_decode(stripslashes($tabs));


		global $KMC_MODULES;

		$save_tabs = array();
		foreach ($tabs as $tab) {
			if ($log) echo "** TAB ** <br>";
			$save_tab = array();
			$save_tab["title"] = $tab->title;
			$save_tab["components"] = array();
			foreach ($tab->components as $component) {
				if ($log) echo "*** TAB COMPONENT *** <br>";
				if (!$component->saved) {
					if (!$component->post->ID) {
						if ($log) echo "ADD NEW ". $component->type ."<br>";
						$new_component_id = $KMC_MODULES[$component->type]->create_instance($component);
						$save_tab["components"][] = $new_component_id;
					}
				} else if ($component->changed) {
					if ($log) echo "UPDATE ". $component->post->post_type ."<br>";
					$instance = $KMC_MODULES[$component->post->post_type]->get_instance($component->post->ID);
					$instance->update($component);
					$save_tab["components"][] = $component->post->ID;
				} else {
					if ($log) echo "DO NOTHING";
					$save_tab["components"][] = $component->post->ID;
				}
			}
			$save_tabs[] = $save_tab;
		}

		if ($log) echo "<br><br>SAVE TABS<br>";
		if ($log) echo var_dump($save_tabs);
		if ($log) echo "<br><hr><br>";
		//if ($log) echo json_encode($save_tabs, JSON_UNESCAPED_UNICODE);
		if ($log) echo json_encode($save_tabs);
		if ($log) echo "<br><hr><br>";
		if ($log) echo json_encode(wp_slash($save_tabs));
		if ($log) echo "<br><hr><br>";
		//update_post_meta($this->id, 'tabs', json_encode($save_tabs, JSON_UNESCAPED_UNICODE));
		//update_post_meta($this->id, 'tabs', json_encode($save_tabs));


		$cleandata = str_replace('\\', '\\\\', json_encode($save_tabs, true));

		update_post_meta($this->id, 'tabs', $cleandata);
		// update_post_meta($this->id, 'tabs', json_encode(wp_slash($save_tabs)));

	}

	public function render () {
		// wp_enqueue_style('kmc_tabs_style', plugins_url('css/tabs.css', __FILE__));
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('kmc_tabs_script', plugins_url('js/tabs.js', __FILE__));

		include("templates/tabs.php");
	}

}


new KMC_Tabs();
