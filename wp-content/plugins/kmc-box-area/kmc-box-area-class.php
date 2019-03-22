<?php

class KMC_Box_Area extends Kloon_Module {

	public $name = "Box Area";
	public $type = "box-area";
	public $create_new = true;
	public $fetch_existing = true;
	public $has_admin_page = true;

	public function __construct () {
		add_action('init', array($this, 'create_post_type'));
		add_action("kcm/enqueue_admin_scripts", array($this, 'enqueue_admin_scripts'));

		parent::__construct();
	}

	public function create_post_type () {
		register_post_type('box-area',
			array(
				'labels' => array(
					'name' => __('Box areas', 'kmc'),
					'singular_name' => __('Box area', 'kmc'),
					'add_new' => __('Add', 'kmc'),
					'add_new_item' => __('Add box area', 'kmc')
				),
    			'show_in_menu' => 'kmc_admin_page',
				'public' => true,
				'publicly_queryable' => false,
				'has_archive' => false,
				'supports' => array('title')
			)
		);
		register_post_type('area-text-box',
			array(
				'public' => false,
				'publicly_queryable' => false,
				'show_in_menu' => true,
				'has_archive' => false,
				'supports' => array('title', 'editor')
			)
		);
	}

	public function enqueue_admin_scripts () {
		wp_enqueue_script('kmc_box_area_admin', plugins_url('js/admin-box-area.js', __FILE__));
		wp_enqueue_style('kmc_box_area_admin', plugins_url('css/admin-box-area.css', __FILE__));
	}

	public function render_admin_templates () {
		include("templates/admin-box-area.php");
	}

	public function get_component_class () {
		return "KMC_Box_Area_Component";
	}

	public function get_new_module_icon () {
		return "<div class='select-module-box'>Box Area</div>";
	}


	public function create_instance ($data, $is_preview=false) {
		$post_id = parent::create_instance($data);
		$instance = $this->get_instance($post_id);
		$instance->update($data);
		return $post_id;
	}
}




class KMC_Box_Area_Box {
	public function __construct ($post) {
		$this->post = $post;

		unset($this->post->post_date);
		unset($this->post->post_date_gmt);
		unset($this->post->post_excerpt);
		unset($this->post->post_status);
		unset($this->post->comment_status);
		unset($this->post->ping_status);
		unset($this->post->post_password);
		unset($this->post->post_name);
		unset($this->post->to_ping);
		unset($this->post->pinged);
		unset($this->post->post_modified);
		unset($this->post->post_modified_gmt);
		unset($this->post->post_content_filtered);
		unset($this->post->post_parent);
		unset($this->post->guid);
		unset($this->post->menu_order);
		unset($this->post->post_mime_type);
		unset($this->post->comment_count);
		unset($this->post->filter);
	}
	public function update ($data) {
		wp_update_post(array(
			'ID' => $data->post->ID,
			'post_title' => $data->post->post_title,
			'post_content' => $data->post->post_content
		));
	}

}
class KMC_Box_Area_Text_Box extends KMC_Box_Area_Box {
	public function __construct ($post) {
		parent::__construct($post);

		$meta_data = get_post_meta($this->post->ID);
		$this->button_text = $meta_data["button_text"][0];
		$this->button_link = $meta_data["button_link"][0];

		if ($this->button_link && is_numeric($this->button_link)) {
			$this->formatted_button_link = get_permalink($this->button_link);
		} else {
			$this->formatted_button_link = $this->button_link;
		}
	}

	public function update ($data) {
		parent::update($data);
		update_post_meta($this->post->ID, 'button_text', $data->button_text);
		update_post_meta($this->post->ID, 'button_link', $data->button_link);
	}
}



class KMC_Box_Area_Preview_Box extends KMC_Box_Area_Box {
	public function __construct ($post) {
		parent::__construct($post);

		$meta_data = get_post_meta($this->post->ID);
		$this->background_image = json_decode($meta_data["background_image"][0]);
		$this->button_text = $meta_data["button_text"][0];
		$this->button_link = $meta_data["button_link"][0];
		$this->cols_md = (int) $meta_data["cols_md"][0];
		$this->rows_md = (int) $meta_data["rows_md"][0];
		$this->cols_sm = (int) $meta_data["cols_sm"][0];
		$this->rows_sm = (int) $meta_data["rows_sm"][0];

		if ($this->button_link && is_numeric($this->button_link)) {			
			$this->formatted_button_link = get_permalink($this->button_link);	
		} else {
			$this->formatted_button_link = $this->button_link;
		}
	}

	public function update ($data) {
		parent::update($data);

		update_post_meta($this->post->ID, 'background_image', json_encode($data->background_image));
		update_post_meta($this->post->ID, 'cols_md', $data->cols_md);
		update_post_meta($this->post->ID, 'rows_md', $data->rows_md);
		update_post_meta($this->post->ID, 'cols_sm', $data->cols_sm);
		update_post_meta($this->post->ID, 'rows_sm', $data->rows_sm);
		update_post_meta($this->post->ID, 'button_text', $data->button_text);
		update_post_meta($this->post->ID, 'button_link', $data->button_link);
	}
}


class KMC_Box_Area_Component extends Kloon_Component {

	public $type = "box-area";

	public function __construct ($post) {
		parent::__construct($post);

		$label = get_post_meta($this->id, 'label', true);
		$this->label = $label;

		$this->boxes = $this->get_all_boxes();
	}

	public function update ($data, $is_revision=false) {
		parent::update($data);

		update_post_meta($this->id, 'label', $data->label);

		$boxes = array();
		foreach ($data->boxes as $box) {

			// Create new post items if needed
			if (!$box->post->ID) {
				$box_id = wp_insert_post(array(
					'post_type' => $box->type,
					'post_status' => 'publish'
				));
				$box->post->ID = $box_id;
			}

			if ($box->type == "area-text-box") {
				$text_box = new KMC_Box_Area_Text_Box($box->post);
				$text_box->update($box);
			} else if ($box->type == "area-preview-box") {
				$text_box = new KMC_Box_Area_Preview_Box($box->post);
				$text_box->update($box);
				// do_action('wpml_register_single_string', 'Lapoint - Box Area', 'Preview Box - Title', $box->post->post_title);
			}

			$boxes[] = $box->post->ID;
		}

		/*
		echo var_dump($boxes);
		echo "<hr>";
		echo var_dump($this->boxes);
		*/

		update_post_meta($this->post->ID, 'box_area_boxes', json_encode($boxes));
	}

	private function get_all_boxes () {
		$boxes = array();
		$boxes_meta = json_decode(get_post_meta($this->post->ID, 'box_area_boxes', true));
		if ($boxes_meta) {
			foreach ($boxes_meta as $post_id) {
				$box = $this->get_box($post_id);
				if ($box) {
					$boxes[] = $box;
				}
			}
		}
		return $boxes;
	}

	private function get_box ($post_id) {
		global $KMC_MODULES;

		$post = get_post($post_id);
		if ($post->post_type == "area-text-box") {
			return new KMC_Box_Area_Text_Box($post);
		} else if ($post->post_type == "area-preview-box") {
			return new KMC_Box_Area_Preview_Box($post);
		} else {
			return false;
		}
	}

	public function render () {
		wp_enqueue_style('kmc_Box_Area', plugins_url('css/box-area.css', __FILE__));

		include("templates/box-area.php");
	}

}


new KMC_Box_Area();