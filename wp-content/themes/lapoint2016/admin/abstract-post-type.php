<?php
/**
 * Abstract class for custom post types
 *
 * @package WordPress
 * @subpackage Lapoint2016
 * @since Lapoint2016 1.0
 */


class Lapoint_Manager {

	public function __construct () {
	}

	public function get ($data) {
		if (gettype($data) == "object") {
			return new $this->instance_class($data);
		} else {
			$post = get_post($data);
			return new $this->instance_class($post);
		}
	}

	public function get_all () {
		if (!$this->_all) {
			global $wpdb;
			$posts = $wpdb->get_results(sprintf("
				SELECT wp.ID, wp.post_title
				FROM %s AS wp
				INNER JOIN %s as wpml ON wp.ID=wpml.element_id
				WHERE wp.post_type='%s' AND wp.post_status='publish' AND wpml.language_code='%s' ORDER BY wp.menu_order",
				$wpdb->posts, $wpdb->prefix . "icl_translations", $this->post_type, ICL_LANGUAGE_CODE)
			);

			$this->_all = $this->as_objects($posts);
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
				WHERE wp.post_type='%s' AND wp.post_status='publish' AND wpml.language_code='%s' ORDER BY wp.menu_order",
				$wpdb->posts, $wpdb->prefix . "icl_translations", $this->post_type, $lang)
			);

			$this->_all = $this->as_objects($posts);
		}
		return $this->_all;
	}


	public function get_all_all_lang () {
		if (!$this->_all_all_lang) {
			$posts = get_posts(array(
				'posts_per_page' => -1,
				'post_type' => $this->post_type,
				'orderby' => 'menu_order',
				'order' => 'ASC',
				'post_status' => 'publish',
				'suppress_filters' => 1
			));

			$this->_all_all_lang = $this->as_objects($posts);
		}

		return $this->_all_all_lang;
	}

	public function get_all_by_meta_key ($key, $value) {
		global $wpdb;
		$posts = $wpdb->get_results(sprintf("
			SELECT wp.ID, wp.post_title
			FROM %s AS wp
			INNER JOIN %s as wpml ON wp.ID=wpml.element_id
			INNER JOIN %s AS wpmeta ON wpmeta.post_id=wp.ID AND wpmeta.meta_key='%s' AND wpmeta.meta_value='%d'
			WHERE wp.post_type='%s' AND wp.post_status='publish' AND wpml.language_code='%s' ORDER BY wp.menu_order",
			$wpdb->posts, $wpdb->prefix . "icl_translations", $wpdb->postmeta, $key, $value, $this->post_type, ICL_LANGUAGE_CODE)
		);

		return $this->as_objects($posts);
	}

	public function get_all_by_meta_keys ($key1, $value1, $key2, $value2) {
		global $wpdb;
		$posts = $wpdb->get_results(sprintf("
			SELECT wp.ID, wp.post_title
			FROM %s AS wp
			INNER JOIN %s as wpml ON wp.ID=wpml.element_id
			INNER JOIN %s AS wpmeta1 ON wpmeta1.post_id=wp.ID AND wpmeta1.meta_key='%s' AND wpmeta1.meta_value='%d'
			INNER JOIN %s AS wpmeta2 ON wpmeta2.post_id=wp.ID AND wpmeta2.meta_key='%s' AND wpmeta2.meta_value='%d'
			WHERE wp.post_type='%s' AND wp.post_status='publish' AND wpml.language_code='%s' ORDER BY wp.menu_order",
			$wpdb->posts, $wpdb->prefix . "icl_translations", $wpdb->postmeta, $key1, $value1, $wpdb->postmeta, $key2, $value2, $this->post_type, ICL_LANGUAGE_CODE)
		);

		return $this->as_objects($posts);
	}

	public function get_all_by_triple_meta_keys ($key1, $value1, $key2, $value2, $key3, $value3) {
		global $wpdb;
		$posts = $wpdb->get_results(sprintf("
			SELECT wp.ID, wp.post_title
			FROM %s AS wp
			INNER JOIN %s as wpml ON wp.ID=wpml.element_id
			INNER JOIN %s AS wpmeta1 ON wpmeta1.post_id=wp.ID AND wpmeta1.meta_key='%s' AND wpmeta1.meta_value='%d'
			INNER JOIN %s AS wpmeta2 ON wpmeta2.post_id=wp.ID AND wpmeta2.meta_key='%s' AND wpmeta2.meta_value='%d'
			INNER JOIN %s AS wpmeta3 ON wpmeta3.post_id=wp.ID AND wpmeta3.meta_key='%s' AND wpmeta3.meta_value='%d'
			WHERE wp.post_type='%s' AND wp.post_status='publish' AND wpml.language_code='%s' ORDER BY wp.menu_order",
			$wpdb->posts, $wpdb->prefix . "icl_translations", $wpdb->postmeta, $key1, $value1, $wpdb->postmeta, $key2, $value2, $wpdb->postmeta, $key3, $value3, $this->post_type, ICL_LANGUAGE_CODE)
		);
		
		return $this->as_objects($posts);
	}

	public function get_all_by_meta_key_without_meta ($key, $value, $exclude_meta) {
		global $wpdb;
		$posts = $wpdb->get_results(sprintf("
			SELECT wp.ID, wp.post_title
			FROM %s AS wp
			INNER JOIN %s as wpml ON wp.ID=wpml.element_id
			INNER JOIN %s AS wpmeta ON wpmeta.post_id=wp.ID AND wpmeta.meta_key='%s' AND wpmeta.meta_value='%d'
			LEFT JOIN %s AS wpmeta_where ON wpmeta_where.post_id=wp.ID AND wpmeta_where.meta_key='%s'
			WHERE wp.post_type='%s' AND wp.post_status='publish' AND wpml.language_code='%s' AND (wpmeta_where.meta_id IS NULL OR wpmeta_where.meta_value='null') ORDER BY wp.menu_order",
			$wpdb->posts, $wpdb->prefix . "icl_translations", $wpdb->postmeta, $key, $value, $wpdb->postmeta, $exclude_meta, $this->post_type, ICL_LANGUAGE_CODE)
		);

		return $this->as_objects($posts);
	}

	public function get_all_by_meta_key_without_metas ($key, $value, $exclude_meta1, $exclude_meta2) {
		global $wpdb;
		$posts = $wpdb->get_results(sprintf("
			SELECT wp.ID, wp.post_title
			FROM %s AS wp
			INNER JOIN %s as wpml ON wp.ID=wpml.element_id
			INNER JOIN %s AS wpmeta ON wpmeta.post_id=wp.ID AND wpmeta.meta_key='%s' AND wpmeta.meta_value='%d'
			LEFT JOIN %s AS wpmeta_where1 ON wpmeta_where1.post_id=wp.ID AND wpmeta_where1.meta_key='%s'
			LEFT JOIN %s AS wpmeta_where2 ON wpmeta_where2.post_id=wp.ID AND wpmeta_where2.meta_key='%s'
			WHERE wp.post_type='%s' AND wp.post_status='publish' AND wpml.language_code='%s' AND (wpmeta_where1.meta_id IS NULL OR wpmeta_where1.meta_value='null')  AND (wpmeta_where2.meta_id IS NULL OR wpmeta_where2.meta_value='null' OR wpmeta_where2.meta_value='0') ORDER BY wp.menu_order",
			$wpdb->posts, $wpdb->prefix . "icl_translations", $wpdb->postmeta, $key, $value, $wpdb->postmeta, $exclude_meta1, $wpdb->postmeta, $exclude_meta2, $this->post_type, ICL_LANGUAGE_CODE)
		);

		return $this->as_objects($posts);
	}

	public function as_objects ($posts) {
		$cls = $this->instance_class;
		return array_map(function ($post) use ($cls) {
			return new $cls($post);
		}, $posts);
	}

}

class Lapoint_PostType {

	public function __construct ($post) {
		$this->id = $post->ID;
		$this->title = $post->post_title;
		$this->content = $post->post_content;
		$this->name = $post->post_name;
		$this->post_type = $post->post_type;
		$this->link = get_permalink($this->id);
	}

	public function get_wpml_permalink () {
		return get_permalink(icl_object_id($this->id, $this->post_type, true));
	}

	public function get_meta_fields () {
		if (!$this->_fields) {
			$this->_fields = get_fields($this->id);
		}

		return $this->_fields;
	}

	public function get_meta_field ($key) {
		$fields = $this->get_meta_fields();
		return $fields[$key];
	}

	public function get_lang_details () {
		if (!$this->_lang_details) {
			$this->_lang_details = apply_filters('wpml_post_language_details', NULL, $this->id);
		}
		return $this->_lang_details;
	}

	public function get_lang_code () {
		$lang_details = $this->get_lang_details();
		return $lang_details["language_code"];
	}

}