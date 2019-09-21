<?php
/**
 * Manager class for Video post type
 *
 * @package WordPress
 * @subpackage Lapoint2016
 * @since Lapoint2016 1.0
 */

class Videos_Manager {

	public function __construct () {
		add_action('init', array($this, 'create_post_type'));
		add_action('acf/include_fields', array($this, 'register_acf_fields'));

		// add_filter("manage_video_posts_columns", array($this, "change_columns"), 20);
		// add_filter("manage_edit-video_sortable_columns", array($this, "sortable_columns"));
	}


	# ****************************** Admin list ******************************
	public function change_columns ($cols) {
		$custom_cols = array_slice($cols, 0, 2);
		$custom_cols["type"] = "Type";
		return array_merge($custom_cols, $cols);
	}

	public function sortable_columns () {
		return array(
			'title' => 'title',
			'type' => 'type',
			'date' => 'date'
		);
	}



	# ****************************** Create Post Types ******************************
	public function create_post_type () {
		register_post_type('lapoint_video',
			array(
				'labels' => array(
					'name' => __('Videos', 'lapoint'),
					'singular_name' => __('Video', 'lapoint'),
					'add_new' => __('Add video', 'lapoint'),
					'add_new_item' => __('Add new video', 'lapoint')
				),
				'public' => true,
				'has_archive' => true,
				'supports' => array('title', 'thumbnail')
			)
		);
	}



	# ****************************** Setup ACF ******************************
	public function register_acf_fields () {
		if (function_exists("register_field_group")) {
			register_field_group(array (
				'id' => 'acf_video-settings',
				'title' => 'Video settings',
				'fields' => array (
					array (
						'key' => 'field_56ec097a0ab01',
						'label' => 'Youtube ID',
						'name' => 'youtube_url',
						'type' => 'text',
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'html',
						'maxlength' => '',
					),
					array (
						'key' => 'field_56ec097a0ab02',
						'label' => 'Width',
						'name' => 'width',
						'type' => 'number',
						'default_value' => '1280',
						'placeholder' => '',
						'maxlength' => '',
					),
					array (
						'key' => 'field_56ec097a0ab03',
						'label' => 'Height',
						'name' => 'height',
						'type' => 'number',
						'default_value' => '720',
						'placeholder' => '',
						'maxlength' => '',
					),
					array (
						'key' => 'field_5866434be37c7',
						'label' => 'Autoplay',
						'name' => 'autoplay',
						'type' => 'true_false',
						'message' => '',
						'default_value' => 1,
					),
				),
			'location' => array (
				array (
					array (
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'lapoint_video',
						'order_no' => 5,
						'group_no' => 5,
					),
				),
			),
				'options' => array (
					'position' => 'normal',
					'layout' => 'default',
					'hide_on_screen' => array (),
				),
				'menu_order' => 0,
			));

		}
	}




	# ****************************** Getters ******************************
	public function get ($data) {
		if (gettype($data) == "object") {
			return new Video($data);
		} else {
			$post = get_post($data);
			return new Video($post);
		}
	}

	public function get_all () {
		if (!$this->_all) {
			global $wpdb;
			$posts = $wpdb->get_results(sprintf("
				SELECT wp.ID, wp.post_title
				FROM %s AS wp
				WHERE wp.post_type='lapoint_video' AND wp.post_status='publish'",
				$wpdb->posts)
			);

			$this->_all = array_map(function ($post) {
				return new Video($post);
			}, $posts);
		}
		return $this->_all;
	}

	public function get_all_in_current_language () {
		if (!$this->_all) {
			global $wpdb;
			$posts = $wpdb->get_results(sprintf("
				SELECT wp.ID, wp.post_title
				FROM %s AS wp
				INNER JOIN %s as wpml ON wp.ID=wpml.element_id
				WHERE wp.post_type='lapoint_video' AND wp.post_status='publish' AND wpml.language_code='%s'",
				$wpdb->posts, $wpdb->prefix . "icl_translations", ICL_LANGUAGE_CODE)
			);

			$this->_all = array_map(function ($post) {
				return new Video($post);
			}, $posts);
		}
		return $this->_all;
	}

	public function get_all_in_lang ($lang) {
		if (!$this->_all) {
			global $wpdb;
			$posts = $wpdb->get_results(sprintf("
				SELECT wp.ID, wp.post_title
				FROM %s AS wp
				INNER JOIN %s as wpml ON wp.ID=wpml.element_id
				WHERE wp.post_type='lapoint_video' AND wp.post_status='publish' AND wpml.language_code='%s' ORDER BY wp.menu_order",
				$wpdb->posts, $wpdb->prefix . "icl_translations", $lang)
			);

			$this->_all = array_map(function ($post) {
				return new Video($post);
			}, $posts);
		}
		return $this->_all;
	}
}


class Video extends Lapoint_PostType {

	public function __construct ($post) {
		parent::__construct($post);

		$fields = $this->get_meta_fields();
		$this->youtube_url = $fields["youtube_url"];
		$this->width = $fields["width"];
		$this->height = $fields["height"];
		$this->autoplay = $fields["autoplay"];
	}

}



global $videos_manager;
$videos_manager = new Videos_Manager();
