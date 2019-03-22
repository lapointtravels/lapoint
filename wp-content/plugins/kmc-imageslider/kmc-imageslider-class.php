<?php

class KMC_Imageslider extends Kloon_Module {

	public $fetch_existing = true;
	public $name = "Imageslider";
	public $type = "imageslider";
	public $category = "custom";

	public function __construct () {
		add_action("kcm/enqueue_admin_scripts", array($this, 'enqueue_admin_scripts'));

		parent::__construct();
	}

	public function enqueue_admin_scripts () {
		wp_enqueue_script('kmc_admin_imageslider', plugins_url('js/admin-imageslider.js', __FILE__));
		wp_enqueue_style('kmc_admin_imageslider', plugins_url('css/admin-imageslider.css', __FILE__));
	}

	public function render_admin_templates () {
		include("templates/admin-imageslider.php");
	}

	public function get_all_instances () {
		global $kloon_image_slider;
		return $kloon_image_slider->get_slideshows();
	}

	public function get_component_class () {
		return "KMC_Imageslider_Component";
	}
}



class KMC_Imageslider_Component extends Kloon_Component {

	public $type = "imageslider";

	public function __construct ($post) {
		parent::__construct($post);

		global $IMAGE_SLIDER_SETTINGS, $kloon_image_slider;
		$upload_dir_url = $IMAGE_SLIDER_SETTINGS["upload_dir_url"];
		$this->slides = $kloon_image_slider->get_slides($post->ID);
		foreach ($this->slides as $slide) {
			$slide->image_url = $upload_dir_url . $slide->filename . "-md." . $slide->type;
		}
	}

	public function render () {
		if ($this->full_width) {
			echo '<div>';
		} else {
			echo '<div class="container">';
		}

		global $kloon_image_slider;
		echo $kloon_image_slider->get_slideshow_output_for_id($this->post->ID);

		echo '</div>';
	}

}


new KMC_Imageslider();
