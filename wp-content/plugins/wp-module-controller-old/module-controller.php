<?php
/*
Plugin Name: Module Controller
Description: Makes it possible to create a page from individual modules
Version: 1.0.0
Author: Christian Wannerstedt @ Kloon Production AB
Author URI: http://www.kloon.se/
License: GPL
Copyright: Christian Wannerstedt
*/


require_once(dirname(__FILE__) .'/lib/utils.php');

require_once(dirname(__FILE__) .'/modules/abstract-module.php');
require_once(dirname(__FILE__) .'/modules/content-module.php');


class Kloon_Module_Controller {

	public function __construct () {
		add_action('add_meta_boxes', array($this, 'kmc_meta_box_add'));
		add_action('save_post', array($this, 'kmc_meta_box_save'));
		//add_action('pre_post_update', array($this, 'pre_post_update'));


		add_action('wp_ajax_get_component_info', array($this, 'ajax_get_component_info'));
		add_action('wp_ajax_fetch_modules', array($this, 'ajax_fetch_modules'));
		add_action('wp_ajax_fetch_module', array($this, 'ajax_fetch_module'));
		add_action('wp_ajax_create_new_component', array($this, 'ajax_create_new_component'));
		add_action('wp_ajax_get_tinymce_editor', array($this, 'ajax_get_tinymce_editor'));
		add_action('wp_ajax_save_content', array($this, 'ajax_save_content'));
		add_action('wp_ajax_update_component', array($this, 'ajax_update_component'));
		add_action('wp_ajax_fetch_posts_for_post_type', array($this, 'ajax_fetch_posts_for_post_type'));
		add_action('wp_ajax_fetch_copy_content_from_post', array($this, 'ajax_fetch_copy_content_from_post'));


		add_action('wp_ajax_module_action', array($this, 'ajax_module_action'));
		add_action('wp_ajax_component_action', array($this, 'ajax_component_action'));

		// add_action('admin_init', array($this, 'remove_editor'));
		add_filter('admin_body_class', array($this, 'add_admin_body_class'));

		global $KMC_MODULES;
		$KMC_MODULES = array();
		add_action('after_setup_theme', array($this, 'after_setup'));

		add_action('admin_menu', array($this, 'my_admin_menu'), 9);
	}

	public function after_setup () {
		do_action("kcm/register_modules");
	}


	public function my_admin_menu () {
		add_menu_page('KMC Components', 'Components', 'manage_options', 'kmc_admin_page', array($this, 'show_kmc_admin_page'), 'dashicons-images-alt2', 100);
	}
	public function show_kmc_admin_page () {
		return "";
	}

	public function get_category_info () {
		$categories = array(
			"basic" => (object) array(
				"title" => "Basic",
				"icon" => plugins_url('img/edit.svg', __FILE__)
			),
			"custom" => (object) array(
				"title" => "Custom",
				"icon" => plugins_url('img/image.svg', __FILE__)
			)
		);

		$categories = apply_filters('kmc/register_categories', $categories);
		return $categories;
	}


	/*
	Use a filter to determine on which admin pages the page composer should be available.
	*/
	public function kmc_meta_box_add () {
		//$admin_types = apply_filters('kmc/register_admin_types', array('page'));
		$admin_types = apply_filters('kmc/register_admin_types', array());
		add_meta_box('kmc-meta-box', 'Page composer', array($this, 'kmc_meta_box'), $admin_types, 'normal', 'high');
	}

	/*
	Remove the editor from all pages where the page composer is available
	*/
	function remove_editor() {
		$admin_types = apply_filters('kmc/register_admin_types', array('page'));
		foreach ($admin_types as $type) {
			remove_post_type_support($type, 'editor');
		}
	}


	function add_admin_body_class($classes) {
		$admin_types = apply_filters('kmc/register_admin_types', array('page'));
		$screen = get_current_screen();
		if ($screen->post_type == "page") {
			$has_kmc = apply_filters('kmc/set_kmc_edit', false);
			if ($has_kmc) {
				$classes .= ' has-kmc';
				//remove_post_type_support('page', 'editor');
				add_meta_box('kmc-meta-box', 'Page composer', array($this, 'kmc_meta_box'), array('page'), 'normal', 'high');
			}
		} else if (in_array($screen->post_type, $admin_types)) {
			$classes .= ' has-kmc';

			// Append an additional class if it's a post
			if ($screen->post_type == "post") {
				$classes .= ' kmc-post';
			}
		}
	    return $classes;
	}



	public function kmc_meta_box () {
		global $post, $kmc_sections, $kmc_module_objects;
		$kmc_sections = $this->get_page_data($post->ID, false);
		//$kmc_sections = array();
		$kmc_module_objects = $this->get_component_objects();

		wp_enqueue_script(array(
			'jquery',
			'jquery-ui-core',
			'jquery-ui-tabs',
			'jquery-ui-sortable',
			'underscore',
			'backbone'
		));

		wp_enqueue_script('kmc-model', plugins_url('js/abstract-component.js', __FILE__));

		do_action("kcm/enqueue_admin_scripts");

		wp_enqueue_script('kmc-admin', plugins_url('js/admin.js', __FILE__));
		wp_enqueue_script('jscolor', plugins_url('js/jscolor.min.js', __FILE__));
		wp_enqueue_style('kmc-admin', plugins_url('css/admin.css', __FILE__));
		wp_enqueue_style('kmc-bootstrap', plugins_url('css/bootstrap.css', __FILE__));




		$this->admin_types = apply_filters('kmc/register_admin_types', array('page'));
		$admin_types = apply_filters('kmc/register_admin_types', array('page'));
		$post_types = array();
		foreach ($admin_types as $admin_type) {
			$post_types[] = get_post_type_object($admin_type); //array($admin_type, get_post_type_object($admin_type));
		}
		$this->post_types = $post_types;



     	wp_nonce_field( 'kmc_meta_box_nonce', 'meta_box_nonce' );
		include("views/meta-box.php");
	}


	/*public function pre_post_update ($post_id) {
		echo "pre_post_update";
	}*/

	public function kmc_meta_box_save ($post_id) {
		$log = false;
		if ($log) echo "ModuleController :: Save :: ". $post_id ."<br>";
		$is_preview = wp_is_post_revision($post_id);


		if ($log) echo (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) ? "Autosave<br>" : "Not-autosave<br>";
		if ($log) echo ($is_preview) ? "IS REVISION<br>" : "NOT REVISION<br>";

		// Bail if we're doing an auto save
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;

		if ($log) echo "CHECK 1<br>";

		// Or if our nonce isn't there, or we can't verify it
		if (!isset($_POST['meta_box_nonce']) || !wp_verify_nonce($_POST['meta_box_nonce'], 'kmc_meta_box_nonce' )) return;

		if ($log) echo "CHECK 2<br>";

		// Or if the current user can't edit this post
		if (!current_user_can('edit_post')) return;


		if ($is_preview) {
			$revision = get_post($post_id);
			$post_id = $revision->post_parent;
			if ($log) echo "Target post = " . $post_id ."<br>";
		}

		if ($log) echo "CHECK 3<br>";


		// Remove action to avoid infinite loops
		remove_action('save_post', array($this, 'kmc_meta_box_save'));

		// Make sure your data is set before trying to save it
		if (isset($_POST['kmc_page_components'])) {
			if ($log) echo "CHECK 4<br>";


			/*if ($log) echo "SAVE 1: " . $_POST['kmc_page_components'] ."<br>";
			if ($log) echo "SAVE 2: " . json_decode($_POST['kmc_page_components']) ."<br>";
			if ($log) echo "SAVE 2: " . json_encode($_POST['kmc_page_components']) ."<br>";*/
			//update_post_meta($post_id, 'kmc_page_components', json_encode($_POST['kmc_page_components']));

			$sections = $_POST['kmc_page_components'];
			/*if ($log) echo "<br>....................<br>";
			if ($log) echo $sections;
			if ($log) echo "<br>....................<br>";
			if ($log) echo stripslashes($sections);
			if ($log) echo "<br>....................<br>";
			*/
			$sections =  json_decode(stripslashes($sections));


			global $KMC_MODULES;

			$save_sections = array();
			foreach ($sections as $section) {
				$save_section = array();
				$save_section["settings"] = $section->settings;
				$save_section["components"] = array();
				foreach ($section->components as $component) {
					if (!$component->saved) {
						if (!$component->post->ID) {
							if ($log) echo "CREATE NEW component<br>";
							$new_component_id = $KMC_MODULES[$component->type]->create_instance($component, $is_preview);

							if ($log) echo "new_component_id: ". $new_component_id ."<br>";
							// When creating new components for a preview, call the update function to save the revision
							if ($is_preview) {

								$instance = $KMC_MODULES[$component->type]->get_instance($new_component_id);
								if ($log) echo "instance: ". var_dump($instance) ."<br>";
								$instance->update($component, $is_preview);
								if ($log) echo "instance->revision_id: " . $instance->revision_id ."<br>";
								$save_section["components"][] = $instance->revision_id;
							} else {
								$save_section["components"][] = $new_component_id;
								$instance = $KMC_MODULES[$component->type]->get_instance($new_component_id);
								$instance->update($component, $is_preview);
							}

						}
					} else if ($component->changed || $is_preview) {
						$instance = $KMC_MODULES[$component->post->post_type]->get_instance($component->post->ID);
						$instance->update($component, $is_preview);

						if ($is_preview) {
							if ($log) echo "Add component with id: " . $instance->revision_id . "<br>";
							$save_section["components"][] = $instance->revision_id;
						} else {
							if ($log) echo "Add component with id: " . $component->post->ID . "<br>";
							//if ($log) echo var_dump($component);
							//if ($log) echo "<br><hr><br>";
							//if ($log) echo var_dump($instance);

							$save_section["components"][] = $component->post->ID;
						}
					} else {
						$save_section["components"][] = $component->post->ID;
					}
				}
				$save_sections[] = $save_section;
			}


			if ($is_preview) {
				if ($log) echo "SAVE REVISION<br>";
				if ($log) echo json_encode($save_sections);
				update_post_meta($post_id, '_kmc_page_components', json_encode($save_sections));
			} else {
				if ($log) echo "SAVE NORMAL<br>";
				if ($log) echo json_encode($save_sections);
				update_post_meta($post_id, 'kmc_page_components', json_encode($save_sections));
			}
		}

		add_action('save_post', array($this, 'kmc_meta_box_save'));
	}



	# ****************************** Helper methods ******************************
	private function get_component_objects () {
		global $KMC_MODULES;
		$kmc_module_objects = array();
		foreach ($KMC_MODULES as $module) {
			$kmc_module_objects[] = $module->get_as_object();
		}

		usort($kmc_module_objects, function($a, $b) {
    		return strcmp($a->name, $b->name);
		});

		return $kmc_module_objects;
	}
	public function get_page_data ($page_id, $is_preview) {
		$kmc_sections = array();

		if ($is_preview) {
			// echo "page_id: ". $page_id ."<br>";
			// echo "meta_key: _kmc_page_components<br>";
			$kmc_page_components_meta = get_post_meta($page_id, '_kmc_page_components', true);
		} else {
			$kmc_page_components_meta = get_post_meta($page_id, 'kmc_page_components', true);
		}

		// echo "PAGE DATA: ". $kmc_page_components_meta ."<br>";

		if ($kmc_page_components_meta) {
			$meta_json = json_decode($kmc_page_components_meta);
			foreach ($meta_json as $section_json) {
				$section = (object) array(
					"components" => array(),
					"settings" => $section_json->settings
				);
				foreach ($section_json->components as $post_id) {
					$component = $this->get_component($post_id, $is_preview);
					if ($component) {
						$section->components[] = $component;
					}
				}
				$kmc_sections[] = $section;
			}
		}
		return $kmc_sections;
	}
	public function get_component ($post_id, $is_preview=false) {
		global $KMC_MODULES;

		if ($is_preview) {
			// echo "Get preview component for ID: ". $post_id ."<br>";
			/*$component_revision_id = get_post_meta($post_id, 'revision_id', true);
			if ($component_revision_id) {
				echo "Has revision<br>";
				$post = get_post($component_revision_id);

				echo var_dump($post);
			} else {
				echo "No haz revision";
				$post = get_post($post_id);
			}*/
			$post = get_post($post_id);
		} else {
			$post = get_post($post_id);
		}


		if ($post && $KMC_MODULES[$post->post_type]) {
			$component_class = $KMC_MODULES[$post->post_type]->get_component_class();

			return new $component_class($post);
		} else {
			return false;
		}
	}



	# ****************************** Ajax ******************************
	public function ajax_get_component_info () {
		$component_id = $_GET["component_id"];
		//$post = get_post($component_id);

		global $wpdb;
		$posts = $wpdb->get_results(sprintf("
			SELECT COUNT(*) AS total_count  FROM %s WHERE meta_key='kmc_page_components' AND meta_value REGEXP '([\[,](%d)[\],])';",
			$wpdb->postmeta, $component_id)
		);
		$count = 0;
		$component_pages = array();

		if ($posts && $posts[0] && $posts[0]->total_count) {
			$count = $posts[0]->total_count;

			$posts = $wpdb->get_results(sprintf("
				SELECT * FROM %s WHERE meta_key='kmc_page_components' AND meta_value REGEXP '([\[,](%d)[\],])';",
				$wpdb->postmeta, $component_id)
			);
			foreach ($posts as $post) {
				$component_pages[] = array(
					"link" => get_edit_post_link($post->post_id),
					"title" => get_the_title($post->post_id)
				);
			}
		}

		json_response(array(
			'status' => 200,
			'data' => array(
				'count' => $count,
				'pages' => $component_pages
			)
		));
	}

	public function ajax_fetch_modules () {
		global $KMC_MODULES;
		$type = $_GET["type"];
		$module_class = $KMC_MODULES[$type];

		json_response(array(
			'status' => 200,
			'data' => array(
				'posts' => $module_class->get_all_instances()
			)
		));
	}

	public function ajax_fetch_module () {
		$post_id = assert_numeric_get("id");
		$post = $this->get_component($post_id);

		json_response(array(
			'status' => 200,
			'data' => array(
				'post' => $post
			)
		));
	}

	public function ajax_fetch_posts_for_post_type () {
		global $KMC_MODULES;
		$type = $_GET["type"];
		$posts = get_posts(array(
			'posts_per_page' => -1,
			'post_type' => $type,
			'orderby' => 'title',
			'order' => 'ASC',
			'post_status' => 'publish',
			'suppress_filters' => 1
		));

		// Add language info if WPLM is active
		if (function_exists('icl_object_id')) {
			foreach ($posts as $post) {
				$post->language_details = apply_filters('wpml_post_language_details', NULL, $post->ID);
			}
		}

		json_response(array(
			'status' => 200,
			'data' => array(
				'posts' => $posts
			)
		));
	}

	public function ajax_fetch_copy_content_from_post () {
		$post_id = assert_numeric_get("id");
		$post = $this->get_component($post_id);

		$kmc_page_components_meta = get_post_meta($post_id, 'kmc_page_components', true);

		$kmc_sections = array();
		if ($kmc_page_components_meta) {
			$meta_json = json_decode($kmc_page_components_meta);
			foreach ($meta_json as $section_json) {
				$section = (object) array(
					"components" => array(),
					"settings" => $section_json->settings
				);
				foreach ($section_json->components as $post_id) {
					$component = $this->get_component($post_id, $is_preview);
					if ($component) {
						$section->components[] = $component;
					}
				}
				$kmc_sections[] = $section;
			}
		}

		json_response(array(
			'status' => 200,
			'data' => array(
				'sections' => $kmc_sections
			)
		));
	}


	public function ajax_create_new_component () {
		$post_type = $_POST["post_type"];
		$post_id = wp_insert_post(array(
			'post_type' => $post_type,
			'post_status'   => 'publish'
		));

		/*
		if (is_wp_error($post_id)) {
			$errors = $post_id->get_error_messages();
			foreach ($errors as $error) {
				echo $error;
			}
		}
		*/

		$post = $this->get_component($post_id);

		json_response(array(
		   'status' => 200,
		   'data' => $post
		));
	}


	public function ajax_get_tinymce_editor () {
		$post_id = $_GET["post_id"];
		$post = get_post($post_id);
		wp_editor($post->post_content, 'txt-editor-' . $post_id);
		exit;
	}

	public function ajax_save_content () {
		$post_id = $_POST["post_id"];
		$post_title = $_POST["post_title"];
		$post_content = $_POST["post_content"];

		wp_update_post(array(
			'ID' => $post_id,
			'post_title' => $post_title,
			'post_content' => $post_content
		));

		$post = $this->get_component($post_id);

		json_response(array(
		   'status' => 200,
		   'data' => $post
		));
	}

	public function ajax_update_component () {
		$post_id = $_POST["post_id"];
		$component = $this->get_component($post_id);

		if ($component) {
			$component->update();

			json_response(array(
			   'status' => 200,
			   'data' => $this->get_component($post_id)
			));
		} else {
			json_response(array(
			   'status' => 404
			));
		}
	}

	public function ajax_module_action () {
		global $KMC_MODULES;
		$module = $_POST["module"];
		return $KMC_MODULES[$module]->action();
	}


	public function ajax_component_action () {
		$post_id = $_POST["post_id"];
		$component = $this->get_component($post_id);

		if ($component) {
			return $component->action();
		} else {
			json_response(array(
			   'status' => 404
			));
		}
	}




	# ****************************** Render Page ******************************
	public function render_page ($page_id, $is_preview=false) {
		//echo "RenderPage ". $page_id .": " . (($is_preview) ? "Preview": "not preview") ."<br>";
		global $kmc_sections, $kmc_module_objects;
		$kmc_sections = $this->get_page_data($page_id, $is_preview);
		$kmc_module_objects = $this->get_component_objects();
		include("views/content.php");
	}

}


global $module_controller;
$module_controller = new Kloon_Module_Controller();
