<?php

class Kloon_Module {

	public $name = "-";
	public $type = "-";
	public $category = "basic";

	// If the component should be available in the new dialog
	public $create_new = false;

	// If the component should be available in the fetch existing dialog
	public $fetch_existing = false;

	// If it should be possible to embed the component in subviews like tabs
	public $sub_support = false;

	// If the component has a admin page
	public $has_admin_page = false;

	public $version = "1.0.0";
	public $requires = array(
		"module-controller" => "1.0.0"
	);

	public function __construct () {
		add_action('init', array($this, 'init'));

		if ($this->has_admin_page) {
			add_action('add_meta_boxes', array($this, 'add_meta_box'));
		}
	}

	public function init () {
		global $KMC_MODULES;
		$KMC_MODULES[$this->type] = $this;
	}

	public function get_as_object () {
		return (object) array(
			"create_new" => $this->create_new,
			"fetch_existing" => $this->fetch_existing,
			"name" => $this->name,
			"type" => $this->type
		);
	}

	public function pimp_instance ($instance) {
		return $instance;
	}

	public function get_all_instances () {
		add_filter('posts_where', array($this, 'filter_post_title'));
		$posts = get_posts(array(
			"post_type" => $this->type,
			'orderby' => 'title',
			'order' => 'ASC',
			'post_status' => 'publish',
			'suppress_filters' => 1,
			'posts_per_page' => -1,
			'meta_key' => 'shared',
			'meta_value' => 1
		));

		if (function_exists('icl_object_id')) {
			foreach ($posts as $post) {
				$post->language_details = apply_filters('wpml_post_language_details', NULL, $post->ID);
			}
		}

		remove_filter('posts_where', array($this, 'filter_post_title'));

		return $posts;
	}


	public function add_meta_box () {
		add_meta_box('component-meta-box', 'KMC Info', array($this, 'component_meta_box'), $this->type, 'normal', 'high');
	}

	public function component_meta_box () {
		global $post, $wpdb;

		$posts = $wpdb->get_results(sprintf("
			SELECT COUNT(*) AS total_count  FROM %s WHERE meta_key='kmc_page_components' AND meta_value REGEXP '([\[,](%d)[\],])';",
			$wpdb->postmeta, $post->ID)
		);

		if ($posts && $posts[0] && $posts[0]->total_count) {
			$count = $posts[0]->total_count;
			echo "This component is used on " . $count . " " . ($count == 1 ? "post" : "posts") . ":<br><br>";

			$posts = $wpdb->get_results(sprintf("
				SELECT * FROM %s WHERE meta_key='kmc_page_components' AND meta_value REGEXP '([\[,](%d)[\],])';",
				$wpdb->postmeta, $post->ID)
			);
			foreach ($posts as $post) {
				echo "<a href=" . get_edit_post_link($post->post_id) . ">" . get_the_title($post->post_id)  . "</a><br>";
			}
		} else {
			echo "This component is not used on any posts.";
		}
	}




	public function get_new_module_icon () {
		return false;
	}

	public function render_admin_templates () {
		return false;
	}

	public function get_component_class () {
		return Kloon_Component;
	}

	public function get_instance ($id) {
		$post = get_post($id);

		$class = $this->get_component_class();
		return new $class($post);
	}

	public function create_instance_from_post ($post) {
		$class = $this->get_component_class();
		return new $class($post);
	}

	public function get_all_posts () {
		return get_posts(array(
			'post_type' => $this->type,
			'posts_per_page' => -1
		));
	}

	public function filter_post_title($where='') {
        global $wpdb;
        $where .= " AND post_title != ''";
        return $where;
    }

	public function create_instance ($data, $is_preview=false) {
		$post_id = wp_insert_post(array_merge((array) $data->post, array(
			'post_type' => $this->type,
			'post_status'   => 'publish'
		)));

		if (isset($data->settings)) {

			update_post_meta($post_id, 'settings', json_encode($data->settings));
		}

		/*
		echo var_dump(array_merge((array) $data->post, array(
			'post_type' => $this->type,
			'post_status'   => 'publish'
		)));
		$post_id = 0;

		if (is_wp_error($post_id)) {
			$errors = $post_id->get_error_messages();
			foreach ($errors as $error) {
				echo $error;
			}
		}*/

		return $post_id;
	}
}

class Kloon_Component {

	public $type = "abstract";
	public $auto_contained = false;

	public function __construct ($post) {
		$this->post = $post;
		$this->id = $this->post->ID;

		// Fetch all meta data for the post
		$this->_meta = get_post_meta($this->id);
		$this->shared = $this->get_meta("shared", false);

		$this->settings = json_decode($this->get_meta("settings"));
		$this->full_width = ($this->settings->width == "full_width");
		$this->is_contained = !$this->full_width;

		// Strip away unused data
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

	public function get_meta($key, $default=false) {
		if (isset($this->_meta[$key])) {
			return $this->_meta[$key][0];
		} else {
			return $default;
		}
	}

	public function get_container_class () {
		if ($this->is_contained) {
			if ($this->settings->width == "extra-contained") {
				return "container extra-contained";
			} else {
				return "container";
			}
		}
		return "";
	}

	public function update ($data, $is_revision=false) {
		$log = false;
		if ($log) echo "UPDATE Component <br>";
		if ($is_revision) {
			if ($log) echo "Is revision<br>";
			$component_revision_id = $this->get_meta("revision_id");
			if ($component_revision_id) {
				unset($data->post->ID);
				if ($log) echo "Has revision id:" . $component_revision_id . "<br>";
				if ($log) echo var_dump((array) $data->post);
				if ($log) echo "<br><hr><br>";
				if ($log) echo var_dump(array('ID' => $component_revision_id));
				if ($log) echo "<hr><br>";
				if ($log) echo "<hr><br>";
				if ($log) echo "<hr><br>";
				if ($log) echo "<hr><br>";

				$post = (array) $data->post;
				$post["ID"] = $component_revision_id;
				if ($log) echo var_dump($post);

				/*if ($log) echo var_dump(array_merge((array) $data->post), array(
					'ID' => $component_revision_id
				));*/
				if ($log) echo "<hr><br>";
				if ($log) echo "<hr><br>";
				if ($log) echo "<hr><br>";
				if ($log) echo "<hr><br>";

				wp_update_post($post);

			} else {
				if ($log) echo "****** CREATE NEW REVISION ******<br>";
				if ($log) echo "Has no revision<br>";
				if ($log) echo var_dump($data->post);
				if ($log) echo "<hr>";


				$pd = (array) $data->post;
				unset($pd->ID);
				$pd["post_status"] = 'inherit';
				$pd["post_name"] = $this->id ."-autosave";
				$pd["post_parent"] = $this->id;

				if ($log) echo "<br>HOHOHO<br>";
				if ($log) echo "<br><hr><br>Insert: ";
				if ($log) echo var_dump($pd);
				if ($log) echo "<br><hr><br>";

				$component_revision_id = wp_insert_post($pd);
				update_post_meta($this->id, 'revision_id', $component_revision_id);
			}
			$this->revision_id = $component_revision_id;
		} else {
			if ($log) echo "Update normal post<br>";
			if ($log) echo "ID:" . $this->id ."<br>";

			if ($log) {
				$a = array_merge((array) $data->post, array(
					'ID' => $this->id
				));
				echo var_dump($a);
			}

			wp_update_post(array_merge((array) $data->post, array(
				'ID' => $this->id
			)));

			update_post_meta($this->id, 'settings', json_encode($data->settings));
			update_post_meta($this->id, 'shared', $data->shared);
		}
	}

	public function extra_classes () {
		return "";
	}

	public function get_style () {
		return "";
	}

	public function render () {
		return;
	}

}
