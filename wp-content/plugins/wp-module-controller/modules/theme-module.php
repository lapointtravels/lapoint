<?php

class Theme_Module extends Kloon_Module {

	public $name = "-";
	public $type = "theme";
	public $category = "theme";

	// If the component should be available in the new dialog
	public $create_new = true;

	// If the component should be available in the fetch existing dialog
	public $fetch_existing = false;

	// If it should be possible to embed the component in subviews like tabs
	public $sub_support = false;

	// If the component has a admin page
	public $has_admin_page = false;

	public $version = "1.0.0";
	public $requires = array(
		"module-controller" => "1.2.0"
	);

	public function __construct ($data) {
		$this->data = $data;
		$this->type = "theme-" . $data->key;

		if (isset($data->sub_support) && $data->sub_support) {
			$this->sub_support = true;
		}

		add_action('init', array($this, 'init'));
	}

	public function init () {
		global $KMC_MODULES;
		$KMC_MODULES[$this->type] = $this;
	}

	public function get_new_module_icon () {
		return "<div class='select-module-box' data-theme-module>" . $this->data->title . "</div>";
	}

	public function get_component_class () {
		return Theme_Component;
	}

	public function get_instance ($id) {
		$post = get_post($id);
		return $this->create_instance_from_post($post);
	}

	public function create_instance_from_post ($post) {
		return new Theme_Component($post, $this->data);
	}

	public function get_as_object () {
		return (object) array(
			"create_new" => $this->create_new,
			"fetch_existing" => $this->fetch_existing,
			"name" => $this->name,
			"type" => $this->type
		);
	}
}


class Theme_Component extends Kloon_Component {

	public $type = "theme";
	public $auto_contained = true;

	public function __construct ($post, $module_data) {
		parent::__construct($post);

		$this->module_data = $module_data;
		$this->type = $this->post->post_type;

		// Populate fields
		foreach ($this->module_data->fields as $field) {
			$field = (object) $field;
			if ($field->type == "image") {
				$this->{$field->key} = json_decode($this->get_meta($field->key));
			} else {
				$this->{$field->key} = $this->get_meta($field->key);
			}
		}
	}

	public function update ($data, $is_revision=false) {
		parent::update($data, $is_revision);

		// Save fields
		foreach ($this->module_data->fields as $field) {
			$field = (object) $field;

			update_post_meta($this->id, $field->key, $data->{$field->key});

			if ($field->type == "image") {
				update_post_meta($this->id, $field->key, json_encode($data->{$field->key}));
			} else {
				update_post_meta($this->id, $field->key, $data->{$field->key});
			}
		}
	}

	public function render () {
		if ($this->module_data->html) {
			echo $this->module_data->html;
		} else {
			include($this->module_data->template);
		}
	}

}
