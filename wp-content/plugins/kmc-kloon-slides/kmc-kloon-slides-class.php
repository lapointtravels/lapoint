<?php

class KMC_Slides extends Kloon_Module {

	public $name = "Slideshow";
	public $type = "kmcslides";
	public $category = "custom";
	public $create_new = true;
	public $fetch_existing = true;

	public $version = "1.0.0";
	public $requires = array(
		"module-controller" => "1.2.14",
	);

	public function __construct () {
		add_action("kcm/enqueue_admin_scripts", array($this, 'enqueue_admin_scripts'));

		parent::__construct();
	}

	public function enqueue_admin_scripts () {
		wp_enqueue_script('kmc_admin_kmckloonslides', plugins_url('js/admin.js', __FILE__));
		wp_enqueue_style('kmc_admin_kmckloonslides', plugins_url('css/admin.css', __FILE__));
	}

	public function render_admin_templates () {
		include("templates/admin.php");
	}

	public function get_new_module_icon () {
		return "<div class='select-module-box'>Slideshow</div>";
	}

	public function get_all_instances () {
		global $kloon_image_slider;
		return $kloon_image_slider->get_slideshows();
	}

	public function get_component_class () {
		return "KMC_Slides_Component";
	}
}



class KMC_Slides_Component extends Kloon_Component {

	public $type = "kmcslides";

	public function __construct ($post) {
		parent::__construct($post);

		$this->slideshow_id = $this->get_meta("slideshow_id");

		/*global $kloon_image_slider;
		$upload_dir_url = $kloon_image_slider->get_settings()["upload_dir_url"];

		$slide_show = $kloon_image_slider->get_slideshow($this->slideshow_id);
		// $slide_show = $kloon_image_slider->get_slideshow($post->ID);
		$slide_images = array();
		foreach ($slide_show->get_slides() as $slide) {
			$slide_images[] = $slide->get_image_url("md");
		}

		$this->slides = $slide_images;*/
	}

	public function update ($data, $is_revision=false) {
		parent::update($data, $is_revision);

		update_post_meta($this->id, "slideshow_id", $data->slideshow_id);
	}

	public function render () {
		if ($this->full_width) {
			echo '<div>';
		} else {
			echo '<div class="container">';
		}

		global $kloon_slides;
		echo $kloon_slides->render($this->slideshow_id);

		echo '</div>';
	}

}


new KMC_Slides();
