<?php
/**
 * @package ImageSlider
 * @version 3.1
 */
/*
Plugin Name: Kloon ImageSlider
Description: A slide show for images and videos
Author: Christian Wannerstedt @ Kloon Production AB
Version: 3.1
Author URI: http://www.kloon.se
*/


require_once(dirname(__FILE__) .'/lib/utils.php');
require_once(dirname(__FILE__) .'/models/slideshow.php');
require_once(dirname(__FILE__) .'/models/slide.php');


class Kloon_Image_Slider {

	public static function getDefaultSettings () {
		return array(
			"plugin_path" => plugin_dir_path(__FILE__),
			"plugin_url" => plugins_url("", __FILE__),
			"upload_url" => plugins_url("views/upload.php", __FILE__),

			"upload_dir_path" => wp_upload_dir()['basedir'] .'/imsl',
			//"upload_dir_path" => "/home/d25504/public_html/clients/lapoint/2016/wp-content/uploads/imsl",
			//"upload_dir_url" => "http://www.lapointcamps.com/wp-content/uploads/imsl/",
			"upload_dir_url" =>  wp_upload_dir()['baseurl'] ."/imsl/",

			//"upload_dir_path" => "/home/d25504/public_html/clients/ikonhus/wp-content/uploads/imsl",
			//"upload_dir_url" => "http://kloon.se/clients/ikonhus/wp-content/uploads/imsl/",
			//"exclude_setting" => array("fixed_height"),
			//"extra_class" => "simple",

			"use_media_library" => False,
			"use_seperate_upload_folders" => True,

			"sizes" => array(
				"xl" => array(2500, 1000),
				"lg" => array(2000, 900),
				"md" => array(1200, 800),
				"sm" => array(770, 400),
				# "xs" => array(600, 200),
				"thumb" => array(150, 100)
			),

			"sizes_order" => array('sm', 'md', 'lg', 'xl'),

			"slide_types" => array(
				array(
					"id" => 1,
					"label" => "Enbart bild",
					"class" => "slide-type-1",
					"choices" => array(),
					"link" => false,
					"title" => false
				),
				array(
					"id" => 2,
					"label" => "Rubrik",
					"class" => "slide-type-2",
					"choices" => array(),
					"link" => false,
					"title" => true
				),
				array(
					"id" => 3,
					"label" => "Text + info (vänsterställd)",
					"class" => "slide-type-3",
					"choices" => array("Text"),
					"link" => false,
					"title" => true
				)
			)
		);
	}

	public static function get_db_slides_table () {
		global $wpdb;
		return $wpdb->prefix .'imsl_slides';
	}


	# ****************************** Cunstructor ******************************
	public function __construct () {
		global $wpdb;
		$this->db_slides_table = $wpdb->prefix .'imsl_slides';

		add_action('init', array($this, 'create_post_type'));
		add_action('admin_menu', array($this, 'setup_admin_menu'));

		register_activation_hook( __FILE__, array($this, 'plugin_activate') );
		register_deactivation_hook( __FILE__, array($this, 'plugin_deactivate') );

		add_action('wp_ajax_update_slide_show_settings', array($this, 'ajax_update_slide_show_settings'));
		add_action('wp_ajax_add_video_slide', array($this, 'ajax_add_video_slide'));
		add_action('wp_ajax_add_image_slide', array($this, 'ajax_add_image_slide'));
		add_action('wp_ajax_delete_slide', array($this, 'ajax_delete_slide'));
		add_action('wp_ajax_update_slide', array($this, 'ajax_update_slide'));
		add_action('wp_ajax_update_slide_position', array($this, 'ajax_update_slide_position'));

		add_action('wp_ajax_get_slideshow', array($this, 'ajax_get_slideshow'));
		add_action('the_content', array($this, 'the_content'));
	}


	# ****************************** Install / Uninstall ******************************
	public function plugin_activate () {
		global $wpdb;

		$structure = "CREATE TABLE ". $this->get_db_slides_table() ." (
			`id` MEDIUMINT( 8 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			`slide_show_id` SMALLINT( 5 ) UNSIGNED NOT NULL ,
			`position` SMALLINT( 5 ) NOT NULL ,
			`slide_type` TINYINT( 1 ) NOT NULL DEFAULT '1',
			`filename` varchar(255) character set utf8 collate utf8_swedish_ci NOT NULL,
			`dirname` varchar(255) character set utf8 collate utf8_swedish_ci NOT NULL,
			`url` varchar(255) character set utf8 collate utf8_swedish_ci NOT NULL,
			`title` varchar(150) character set utf8 collate utf8_swedish_ci NOT NULL,
			`text1` varchar(150) character set utf8 collate utf8_swedish_ci NOT NULL,
			`text2` varchar(150) character set utf8 collate utf8_swedish_ci NOT NULL,
			`text3` varchar(150) character set utf8 collate utf8_swedish_ci NOT NULL,
			`link` varchar(255) character set utf8 collate utf8_swedish_ci NOT NULL,
			`type` varchar(20) character set utf8 collate utf8_swedish_ci NOT NULL,
			`mime` varchar(20) character set utf8 collate utf8_swedish_ci NOT NULL,
			`video_type` varchar(10) character set utf8 collate utf8_swedish_ci NOT NULL,
			`video_id` varchar(255) character set utf8 collate utf8_swedish_ci NOT NULL,
			`autoplay` TINYINT( 1 ) NOT NULL DEFAULT '1',
			`keep_proportions` TINYINT( 1 ) NOT NULL DEFAULT '1',
			`vertical_align` varchar(10) NOT NULL DEFAULT '1',
			`width` smallint(5) unsigned NOT NULL,
			`height` smallint(5) unsigned NOT NULL,
			`image_data` TEXT NOT NULL DEFAULT '',
			`created` DATETIME NOT NULL ,
			`updated` DATETIME NOT NULL ,
			INDEX ( `slide_show_id` )
			) DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci";

		$wpdb->query($structure);
	}
	public function plugin_deactivate () {
		global $wpdb;
		$wpdb->query("DROP TABLE IF EXISTS `". $this->get_db_slides_table() ."`;");
	}



	# ****************************** Setup ******************************
	public function create_post_type () {
		register_post_type('imageslider',
			array(
				'labels' => array(
					'name' => __('Slide shows', 'imageslider'),
					'singular_name' => __('Slide show', 'imageslider'),
					'add_new' => __('Add new', 'imageslider'),
					'add_new_item' => __('Add new slide show', 'imageslider')
				),
				'public' => false,
				'has_archive' => false,
				'supports' => array()
			)
		);
	}

	public function setup_admin_menu() {
		# Add the menu button
		add_menu_page('Manage slide shows', 'Slide shows', 'manage_options', 'imageslider-index', array($this, 'render_index_view'), 'dashicons-format-gallery', 3);

		add_submenu_page('imageslider-index', 'New slide show', 'New slide show', 'manage_options', 'imageslider-new', array($this, 'render_new_view'));

		add_submenu_page('imageslider-edit-slide-show', 'Edit slide show', 'Edit slide show', 'manage_options', 'imageslider-edit-slide-show', array($this, 'render_edit_view'));
	}

	public function get_settings () {
		if (!$this->_settings) {
			$this->_settings = apply_filters('imageslider_settings', Kloon_Image_Slider::getDefaultSettings());
		}

		return $this->_settings;
	}


	// ****************************** Admin Index View ******************************
	public function render_index_view () {
		assert_admin_access();

		// Delete slide show
		if (is_admin_action("delete")){
			global $wpdb;
			$slide_show_id = assert_numeric_post("sid");
			$slide_show = $this->get_slideshow($slide_show_id);
			$slide_show->delete();
		}

		global $slide_shows;
		$slide_shows = $this->get_slideshows();

		wp_enqueue_style('imageslider-admin-style', plugins_url('css/admin.css', __FILE__));

		require_once(dirname(__FILE__) . '/views/admin-index.php');
	}



	// ****************************** Admin New View ******************************
	public function render_new_view () {
		assert_admin_access();

		if (is_admin_action("create") && isset($_POST["slide_show_title"])) {

			// Save new slide show
			$post_id = wp_insert_post(array(
				'post_type' => "imageslider",
				'post_title' => $_POST["slide_show_title"],
				'post_status' => 'publish'
			));

			// $fixed_height = isset($_POST["slide_show_fixed_height"]) ? $_POST["slide_show_fixed_height"] : "false";
			$size = (isset($_POST["slide_show_size"]) && $_POST["slide_show_size"]) ? $_POST["slide_show_size"] : "dynamic";
			update_post_meta($post_id, 'size', $size);

			$this->render_index_view();

		} else {

			wp_enqueue_script('jquery');
			wp_enqueue_style('imageslider_admin_style', plugins_url('css/admin.css', __FILE__));

			require_once(dirname(__FILE__) .'/views/admin-new-slide-show.php');
		}
	}



	// ****************************** Admin Edit View ******************************
	public function render_edit_view () {
		global $slide_show;

		// Access and input validation
		assert_admin_access();
		$slide_show_id = assert_numeric_get("slide_show_id");

		// Check if the slide show exists
		$slide_show = $this->get_slideshow($slide_show_id);
		if (!$slide_show){
			wp_die( __('The specified slide show does not exist.') );
		}

		// Add js libs
		wp_enqueue_script(array(
			"jquery",
			"jquery-effects-core",
			"jquery-ui-core",
			"jquery-ui-widget",
			"jquery-ui-mouse",
			"jquery-ui-sortable",
			"underscore",
			"backbone"
		));

		wp_enqueue_media();

		wp_enqueue_script('plupload', plugins_url('js/libs/plupload/js/plupload.js', __FILE__));
		wp_enqueue_script('plupload-html5', plugins_url('js/libs/plupload/js/plupload.html5.js', __FILE__));
		wp_enqueue_script('plupload-flash', plugins_url('js/libs/plupload/js/plupload.flash.js', __FILE__));
		wp_enqueue_script('plupload-html4', plugins_url('js/libs/plupload/js/plupload.html4.js', __FILE__));

		// Add own scripts
		wp_enqueue_script('imsl-pluploader', plugins_url('js/pluploader.js', __FILE__));
		wp_enqueue_script('imsl-edit-slide-show', plugins_url('js/admin-edit-slide-show.js', __FILE__), array(), 3);

		wp_enqueue_style('imageslider-admin-style', plugins_url('css/admin.css', __FILE__));

		require_once(dirname(__FILE__) .'/views/admin-edit-slide-show.php');
	}



	private function _copy_all_images_to_media_library () {
		global $wpdb;

		// $slide_show = $this->get_slideshow(6280);
		// $slide_show_id = assert_numeric_post("slide_show_id");
		// $slide_show = $this->get_slideshow($slide_show_id);
		$slideshows = $this->get_slideshows();

		foreach ($slideshows as $slide_show) {

			$slides = $slide_show->get_slides();

			foreach ($slides as $slide) {
				if (isset($slide->image_data) && $slide->image_data && isset($slide->image_data->thumbnail) && strrpos($slide->image_data->thumbnail, ".jpg")) {
					// echo "\nSKIP " . $slide->id . "\n";
					/*echo var_dump($slide->image_data);
					echo "\n***** > \n";
					echo strrpos($slide->image_data->thumbnail, ".jpg");
					echo "\n < *****\n";*/
					continue;
				}

				echo "\n*********************\n";
				// var_dump($slide);
				/*echo $slide->image_data;
				if ($slide->image_data) {
					echo "!!!";
				}*/

				// echo $slide->get_image_path();
				$file = $slide->get_image_path();
				// echo $file;

				// $file = str_replace("/home/d25504/public_html/clients/lapoint/2016/", "/htdocs/lapoint/lapoint2016/public_html/", $file);
				echo "File: ". $file . "\n";

				$filename = basename($file);
				$upload_file = wp_upload_bits($filename, null, file_get_contents($file));
				if (!$upload_file['error']) {

					$parent_post_id = 0;
					$wp_filetype = wp_check_filetype($filename, null );
					echo "\n__ CHECK __\n";
					echo $wp_filetype['type'];
					echo "\n____________\n";

					/*
					'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
					'post_mime_type' => $filetype['type'],
					'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
					'post_content'   => '',
					'post_status'    => 'inherit'
					*/
					$attachment = array(
						'guid' => $upload_file["url"],
						'post_mime_type' => $wp_filetype['type'],
						'post_parent' => $parent_post_id,
						'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
						'post_content' => '',
						'post_status' => 'inherit'
					);
					$attachment_id = wp_insert_attachment( $attachment, $upload_file['file'], $parent_post_id);

					echo "\n attachment_id: " . $attachment_id . "\n";
					echo "upload_file['file']: " . $upload_file['file'] . "\n";
					echo "parent_post_id: " . $parent_post_id . "\n";
					if (!is_wp_error($attachment_id)) {

						/*apply_filters('wp_handle_upload', array(
						    'file' => $upload_file["file"],
						    'url' => $upload_file["url"],
						    'type' => $upload_file["type"]),
						'upload');*/


						require_once(ABSPATH . "wp-admin" . '/includes/image.php');
						$upload_file['post_mime_type'] = $upload_file['type'];
						$attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_file['file'] );
						wp_update_attachment_metadata( $attachment_id,  $attachment_data );

						$url = dirname($upload_file["url"]);

						$image_data = array();
						$image_data["thumbnail"] = $url . "/" . $attachment_data["sizes"]["thumbnail"]["file"];
						$image_data["sizes"] = array();
						$image_data["sizes"]["image-2000"] = $attachment_data["sizes"]["image-2000"];
						if ($image_data["sizes"]["image-2000"]["file"]) {
							$image_data["sizes"]["image-2000"]["url"] = $url . "/" . $attachment_data["sizes"]["image-2000"]["file"];
						}
						$image_data["sizes"]["image-1200"] = $attachment_data["sizes"]["image-1200"];
						$image_data["sizes"]["image-1200"]["url"] = $url . "/" . $attachment_data["sizes"]["image-1200"]["file"];
						$image_data["sizes"]["image-770"] = $attachment_data["sizes"]["image-770"];
						$image_data["sizes"]["image-770"]["url"] = $url . "/" . $attachment_data["sizes"]["image-770"]["file"];

						echo "\nSlide ID:" . $slide->id;
						echo "**** upload_file: \n";
						echo var_dump($upload_file);
						echo "**** attachment_data: \n";
						echo var_dump($attachment_data);
						echo "**** image_data: \n";
						echo var_dump($image_data);

						$wpdb->update(
							$this->db_slides_table,
							array(
								'image_data' => json_encode($image_data)
							),
							array('id' => $slide->id),
							array('%s'), // Updated formats
							array('%d') // Where formats
						);

						echo "\n\n\n\n";


					} else {
						echo "Error 2";
					}
				} else {
					echo "ERROR!";
					echo $upload_file['error'];
				}
			}
		}
	}

	// ****************************** Update slide show settings (AJAX) ******************************
	public function ajax_update_slide_show_settings () {
		/*$slideshows = $this->get_slideshows();
		foreach ($slideshows as $slide_show) {
			// update_post_meta($this->id, 'size', $size);
			echo "!!! Slide show " . $slide_show->id . " !!! >\n";
			echo $slide_show->size . "\n";
			if (!$slide_show->size) {
				echo "DO UPDATE\n";
				update_post_meta($slide_show->id, 'size', "fixed");
			}
			echo "<!!!\n\n";
		}

		exit();


		$this->_copy_all_images_to_media_library();
		exit();*/

		$slide_show_id = assert_numeric_post("slide_show_id");
		$slide_show = $this->get_slideshow($slide_show_id);
		$slide_show->update();
		json_reponse(200);
	}





	// ****************************** Add new video slide (AJAX) ******************************
	public function ajax_add_video_slide () {
		global $wpdb;

		$slide_show_id = assert_numeric_post("slide_show_id");

		$wpdb->insert(
			$this->db_slides_table,
			array(
				'slide_show_id' => $slide_show_id,
				'video_type' => $_POST["video_type"],
				'video_id' => $_POST["video_id"],
				'width' => $_POST["width"],
				'height' => $_POST["height"],
				'created' => "NOW()",
				'updated' => "NOW()"
			),
			array('%d', '%s', '%s', '%d', '%d', '%s', '%s')
		);

		json_reponse(array(
			"id" => $wpdb->insert_id
		));
	}


	# ****************************** Add new image slide (media library) ******************************
	public function ajax_add_image_slide () {
		global $wpdb;

		$slide_show_id = assert_numeric_post("slide_show_id");
		$image_data = json_encode($_POST["data"]);

		$wpdb->insert(
			$this->db_slides_table,
			array(
				'slide_show_id' => $slide_show_id,
				'image_data' => $image_data,
				'created' => "NOW()",
				'updated' => "NOW()"
			),
			array('%d', '%s', '%s', '%s')
		);

		json_reponse(array(
			"id" => $wpdb->insert_id
			// "image_data" => $_POST["data"];
		));
	}


	// ****************************** Delete slide (AJAX) ******************************
	public function ajax_delete_slide () {
		assert_admin_access();

		global $wpdb;
		$slide_id = assert_numeric_post("slide_id");


		$settings = $this->get_settings();
		$media_library = $settings["use_media_library"] ? true : false;

		// Delete image files
		$result = $wpdb->get_results(sprintf(
			"SELECT * FROM `%s` WHERE id=%d LIMIT 1;",
			$this->db_slides_table,
			$slide_id
		));
		if ($result){
			if ($media_library) {
				foreach ($result as $slide){
					if ($slide->dirname) {
						delete_file($slide->dirname .'/'. $slide->filename .'.'. $slide->type);
						delete_file($slide->dirname .'/'. $slide->filename .'-thumb.'. $slide->type);
						delete_file($slide->dirname .'/'. $slide->filename .'-lg.'. $slide->type);
						delete_file($slide->dirname .'/'. $slide->filename .'-md.'. $slide->type);
						delete_file($slide->dirname .'/'. $slide->filename .'-sm.'. $slide->type);
					}
				}
			}

			// Delete record
			$wpdb->delete(
				$this->db_slides_table,
				array('id' => $slide_id),
				array('%d')
			);
		}

		json_reponse(200);
	}


	// ****************************** Update slide (AJAX) ******************************
	public function ajax_update_slide () {
		global $wpdb;

		$slide_id = assert_numeric_post("slide_id");

		if (isset($_POST["video_type"]) && $_POST["video_type"]) :
			$wpdb->update(
				$this->db_slides_table,
				array(
					'video_type' => $_POST["video_type"],
					'video_id' => $_POST["video_id"],
					'width' => $_POST["width"],
					'height' => $_POST["height"],
					'autoplay' => ($_POST["autoplay"] == "true"),
					'keep_proportions' => ($_POST["keep_proportions"] == "true"),
					'updated' => "NOW()",
				),
				array('id' => $slide_id),
				array('%s', '%s', '%d', '%d', '%d', '%d', '%s'), // Updated formats
				array('%d') // Where formats
			);

		else:

			$wpdb->update(
				$this->db_slides_table,
				array(
					'title' => $_POST["title"],
					'link' => $_POST["link"],
					'slide_type' => $_POST["slide_type"],
					'text1' => $_POST["text1"],
					'text2' => $_POST["text2"],
					'text3' => $_POST["text3"],
					'vertical_align' => $_POST["vertical_align"]
				),
				array('id' => $slide_id),
				array('%s', '%s', '%s', '%s', '%s', '%s'), // Updated formats
				array('%d') // Where formats
			);

		endif;

		json_reponse(200);
	}


	// ****************************** Update slide position (AJAX) ******************************
	public function ajax_update_slide_position () {
		global $wpdb;

		if (isset($_POST["ids"])){

			$ids = $_POST["ids"];
			$arrIds = explode(",",  $ids);
			$position = 0;
			foreach ($arrIds as $id) {
				$wpdb->update(
					$this->db_slides_table,
					array('position' => $position),
					array('id' => $id),
					array('%d'), // Updated formats
					array('%d') // Where formats
				);
				$position++;
			}

			json_reponse(200);

		} else {
			json_reponse(500);
		}

		die();
	}

	// ****************************** Show slide shows in content ******************************
	// ****************************** Get single slide show (AJAX) ******************************
	public function ajax_get_slideshow () {
		$slide_show_id = assert_numeric_get("slide_show_id");
		$slide_show_html = $this->get_slideshow_output_for_id($slide_show_id);

		json_response(array(
			'status' => 200,
			'id' => $id,
			'html' => $slide_show_html
		));
	}

	public function the_content ($content) {
		$pattern = '/\[SLIDE_SHOW_(\d+)\]/';
		preg_match_all($pattern, $content, $matches);

		if ($matches[0]){
			for ($i=0; $i<count($matches[0]) ; $i++) {
				// Get the slide show
				$id = $matches[1][$i];
				$slide_show_html = $this->get_slideshow_output_for_id($id);
				$content = str_replace($matches[0][$i], $slide_show_html, $content);
			}

		}

		return $content;
	}

	public function get_slideshow_output_for_id ($id) {
		$slideshow_html = "";
		$slide_show = $this->get_slideshow($id);
		if ($slide_show) {
			$image_dir = plugins_url('images/', __FILE__);

			// Added necessary js and css files
			wp_enqueue_script(array(
				'jquery',
				'jquery-effects-core'
			));

			wp_enqueue_script('imageslider-slideshow', plugins_url('js/imageslider-slideshow.js', __FILE__));
			wp_enqueue_style('imageslider-style', plugins_url('css/imageslider.css', __FILE__));

			// Construct output
			ob_start();
			include("templates/slideshow.php");
			$slideshow_html = ob_get_contents();
			ob_end_clean();
		}
		return $slideshow_html;
	}

	public function get_slides ($slide_show_id) {
		global $wpdb;
		return $wpdb->get_results(sprintf(
			"SELECT * FROM `%s` WHERE slide_show_id='%d' ORDER BY position;",
			$this->db_slides_table,
			$slide_show_id
		));
	}


	public function get_slideshow ($id) {
		return new Kloon_Slide_Show ($id);
	}

	public function get_slideshows () {
		if (!$this->_all) {
			$posts = get_posts(array(
				'post_type' => 'imageslider',
				'posts_per_page' => -1,
				'orderby' => 'title',
				'order' => 'ASC'
			));

			$this->_all = array_map(function ($post) {
				return new Kloon_Slide_Show($post);
			}, $posts);
		}

		return $this->_all;
	}

}

global $kloon_image_slider;
$kloon_image_slider = new Kloon_Image_Slider();
