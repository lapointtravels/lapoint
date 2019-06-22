<?php
/**
 * Manager class for Package post type
 *
 * @package WordPress
 * @subpackage Lapoint2016
 * @since Lapoint2016 1.0
 */

class Packages_Manager extends Lapoint_Manager {

	public $instance_class = "Package";
	public $post_type = "package";


	public function __construct () {
		parent::__construct();

		add_action('init', array($this, 'create_post_type'));
		add_action('acf/register_fields', array($this, 'register_acf_fields'));

		add_filter("manage_package_posts_columns", array($this, "change_columns"), 20);
		add_filter("manage_edit-package_sortable_columns", array($this, "sortable_columns"));

		add_filter('package_rewrite_rules', array($this, 'add_permastruct'));
		add_filter('post_type_link', array($this, 'custom_post_permalink'), 10, 4);
	}



	# ****************************** URL ******************************
	public function add_permastruct( $rules ) {
		global $wp_rewrite;

	    // set your desired permalink structure here
	    $struct = '/%desttype%/%dest%/levels/%postname%/';

	    // use the WP rewrite rule generating function
	    $rules = $wp_rewrite->generate_rewrite_rules(
	        $struct,       // the permalink structure
	        EP_PERMALINK,  // Endpoint mask: adds rewrite rules for single post endpoints like comments pages etc...
	        false,         // Paged: add rewrite rules for paging eg. for archives (not needed here)
	        true,          // Feed: add rewrite rules for feed endpoints
	        false,          // For comments: whether the feed rules should be for post comments - on a singular page adds endpoints for comments feed
	        false,         // Walk directories: whether to generate rules for each segment of the permastruct delimited by '/'. Always set to false otherwise custom rewrite rules will be too greedy, they appear at the top of the rules
	        true           // Add custom endpoints
	    );

	    return $rules;
	}


	public function custom_post_permalink( $permalink, $post, $leavename, $sample ) {

	    // only do our stuff if we're using pretty permalinks
	    // and if it's our target post type
	    if ($post->post_type == 'package' && get_option('permalink_structure')) {
	        // remember our desired permalink structure here
	        // we need to generate the equivalent with real data
	        // to match the rewrite rules set up from before

	        $struct = '%desttype%/%dest%/levels/%postname%/%lang%';

	        $log = false;

	        if ($log) echo "!!!!!" . $permalink;

	        $rewritecodes = array(
	            '%desttype%',
	            '%dest%',
	            '%postname%',
	            '%lang%'
	        );

	        $desttype = '';
	        // for local dev and staging WPML can be set to use query mode for translations to work.
					parse_str( parse_url( $permalink )["query"], $parsed_query );
	        

	        $destination = get_field("destination", $post->ID);

	        $dest = $destination->post_name;
	        $desttype = get_field("destination_type", $destination->ID)->post_name;

          if (empty($desttype)) {
            $desttype = "missing-data";
          }
          if (empty($dest)) {
            $dest = "missing-data";
          }


            // Append location if the camp has one
	        $location = get_field("location", $post->ID);
          if ($location) {
          	$dest .= "/" . $location->post_name;
          }

	        $replacements = array(
            $desttype,
            $dest,
            ($leavename) ? '%package%' : $post->post_name,
            $parsed_query['lang'] ? '?lang=' . $parsed_query['lang'] : ''
	        );

	        // finish off the permalink
	        $permalink = get_wpml_home_url($permalink) . str_replace($rewritecodes, $replacements, $struct);
	        if( !$parsed_query['lang'] ) {
		        $permalink = user_trailingslashit($permalink, 'single');	        	
	        }

	        if ($log) echo " --> " . $permalink. "!!!!!!<br>";
	    }
	    return $permalink;
	}



	# ****************************** Admin list ******************************
	public function change_columns( $cols ) {
		$custom_cols = array_slice($cols, 0, 2);
		$custom_cols["destination"] = "Destination";
		$custom_cols["location"] = "Location";
		$custom_cols["level"] = "Level";
		return array_merge($custom_cols, $cols);
	}
	public function sortable_columns() {
		return array(
			'title' => 'title',
			'destination' => 'destination',
			'location' => 'location',
			'level' => 'level',
			'date' => 'date'
		);
	}





	# ****************************** Create Post Types ******************************
	public function create_post_type () {
		register_post_type('package',
			array(
				'labels' => array(
					'name' => __('Packages', 'lapoint'),
					'singular_name' => __('Package', 'lapoint'),
					'add_new' => __('Add package', 'lapoint'),
					'add_new_item' => __('Add new package', 'lapoint')
				),
				'public' => true,
				'has_archive' => true,
				'supports' => array('title', 'editor', 'thumbnail'),
				'menu_position' => 51,
        		'menu_icon' => 'dashicons-tickets-alt',
        		/*'rewrite' => array(
	                'slug' => 'package',
	                'with_front' => false
				)*/
				'publicly_queryable' => true,
				'rewrite' => false,
				'query_var' => true,
			)
		);
	}



	# ****************************** Setup ACF ******************************
	public function register_acf_fields () {
		register_field_group(array (
			'id' => 'acf_packages-settings',
			'title' => 'Package settings',
			'fields' => array (
				array (
					'key' => 'field_5710a5f0920e0',
					'label' => 'Parent Package',
					'name' => 'parent_package',
					'type' => 'post_object',
					'instructions' => 'Select parent if this package is a sub package.',
					'post_type' => array (
						0 => 'package',
					),
					'taxonomy' => array (
						0 => 'all',
					),
					'allow_null' => 1,
					'multiple' => 0,
				),
				array (
					'key' => 'field_56ec009725b4c',
					'label' => 'Destination',
					'name' => 'destination',
					'type' => 'post_object',
					'post_type' => array (
						0 => 'destination',
					),
					'taxonomy' => array (
						0 => 'all',
					),
					'allow_null' => 0,
					'multiple' => 0,
				),
				array (
					'key' => 'field_5710cb3044e81',
					'label' => 'Location',
					'name' => 'location',
					'type' => 'post_object',
					'post_type' => array (
						0 => 'location',
					),
					'taxonomy' => array (
						0 => 'all',
					),
					'allow_null' => 1,
					'multiple' => 0,
				),
				array (
					'key' => 'field_56ec00a625b4d',
					'label' => 'Level',
					'name' => 'level',
					'type' => 'post_object',
					'post_type' => array (
						0 => 'level',
					),
					'taxonomy' => array (
						0 => 'all',
					),
					'allow_null' => 0,
					'multiple' => 0,
				),
				array (
					'key' => 'field_572c459a06f66',
					'label' => 'Durations',
					'name' => 'durations',
					'type' => 'select',
					'instructions' => '(Optional) Select the durations that are available for the package. This will override the duration settings for the destination',
					'choices' => array (
						'WE' => __("Weekend", "lapoint"),
						1 => __("1 day", "lapoint"),
						2 => __("2 days", "lapoint"),
						3 => __("3 days", "lapoint"),
						4 => __("4 days", "lapoint"),
						5 => __("5 days", "lapoint"),
						6 => __("6 days", "lapoint"),
						7 => __("1 week", "lapoint"),
						14 => __("2 week", "lapoint"),
						21 => __("3 week", "lapoint")
					),
					'default_value' => '',
					'allow_null' => 0,
					'multiple' => 1,
				),
			),
			'location' => array (
				array (
					array (
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'package',
						'order_no' => 3,
						'group_no' => 3,
					),
				),
			),
			'options' => array (
				'position' => 'normal',
				'layout' => 'default',
				'hide_on_screen' => array (
				),
			),
			'menu_order' => 0,
		));

		register_field_group(array (
			'id' => 'acf_package-box',
			'title' => 'Package Box',
			'fields' => array (
				array (
					'key' => 'field_56ec097a0ec62',
					'label' => 'Button text',
					'name' => 'box_button_text',
					'type' => 'text',
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'formatting' => 'html',
					'maxlength' => '',
				),
				array (
					'key' => 'field_56ec097a0ecc5',
					'label' => 'Background image',
					'name' => 'box_background_image',
					'type' => 'image',
					'save_format' => 'object',
					'preview_size' => 'thumbnail',
					'library' => 'all',
				),
				array (
					'key' => 'field_56ec097a0eccf',
					'label' => 'Box columns, large',
					'name' => 'box_width_md',
					'type' => 'select',
					'instructions' => 'How many columns should the box span when the browser width is larger than 992px.',
					'choices' => array (
						4 => '1 column',
						8 => '2 columns',
					),
					'default_value' => '1 column',
					'allow_null' => 0,
					'multiple' => 0,
				),
				array (
					'key' => 'field_57023e8ebf432',
					'label' => 'Box rows, large',
					'name' => 'box_height_md',
					'type' => 'select',
					'instructions' => 'How many rows should the box span when the browser width is larger than 992px.',
					'choices' => array (
						1 => '1 row',
						2 => '2 rows',
					),
					'default_value' => '1 row',
					'allow_null' => 0,
					'multiple' => 0,
				),
				array (
					'key' => 'field_56ec097a0ecd9',
					'label' => 'Box columns, medium',
					'name' => 'box_width_sm',
					'type' => 'select',
					'instructions' => 'How many columns should the box span when the browser width is between 768px and 992px.',
					'choices' => array (
						6 => '1 column',
						12 => '2 columns',
					),
					'default_value' => '',
					'allow_null' => 0,
					'multiple' => 0,
				),
				array (
					'key' => 'field_57024f90d0764',
					'label' => 'Box rows, medium',
					'name' => 'box_height_sm',
					'type' => 'select',
					'instructions' => 'How many columns should the box span when the browser width is between 768px and 992px.',
					'choices' => array (
						1 => '1 row',
						2 => '2 rows',
					),
					'default_value' => '1 row',
					'allow_null' => 0,
					'multiple' => 0,
				),
			),
			'location' => array (
				array (
					array (
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'package',
						'order_no' => 5,
						'group_no' => 5,
					),
				),
			),
			'options' => array (
				'position' => 'normal',
				'layout' => 'default',
				'hide_on_screen' => array (
				),
			),
			'menu_order' => 10,
		));
	}


	# ****************************** Getters ******************************
	public function get_all_by_destination ($destination_id) {
		return $this->get_all_by_meta_key_without_meta("destination", $destination_id, "parent_package");
	}
	public function get_all_by_location ($location_id) {
		return $this->get_all_by_meta_key_without_meta("location", $location_id, "parent_package");
	}
	public function get_sub_packages ($package_id) {
		return $this->get_all_by_meta_key("parent_package", $package_id);
	}
	public function get_by_destination_and_level ($destination, $level) {
		return $this->get_all_by_meta_keys("destination", $destination, "level", $level);
	}

	public function get_by_destination_location_and_level ($destination, $location, $level) {
		return $this->get_all_by_triple_meta_keys("destination", $destination, "location", $location, "level", $level);
	}
}


class Package extends Lapoint_PostType {

	public function __construct ($post) {
		parent::__construct($post);

		$fields = $this->get_meta_fields();
		$this->booking_code = $fields["booking_code"];
		$this->durations = $fields["durations"];
	}

	public function get_level () {
		if (!$this->_level) {
			$fields = $this->get_meta_fields();
			$level = $fields["level"];

			global $levels_manager;
			$this->_level = $levels_manager->get($level);
		}

		return $this->_level;
	}


	public function get_packages () {
		if (!$this->_packages) {
			global $packages_manager;
			$this->_packages = $packages_manager->get_sub_packages($this->id);
		}
		return $this->_packages;
	}

	public function get_box_info () {
		if (!$this->box) {
			$fields = $this->get_meta_fields();

			$this->box = (object) array();
			$this->box->background_image = $fields["box_background_image"];
			$this->box->width_md = $fields["box_width_md"];
			$this->box->width_sm = $fields["box_width_sm"];
			$this->box->height_md = $fields["box_height_md"];
			$this->box->height_sm = $fields["box_height_sm"];
			//$this->box->info_header = $fields["box_header"];
			//$this->box->info_body = $fields["box_body"];
			$this->box->button_text = $fields["box_button_text"];
		}

		return $this->box;
	}

	public function get_location () {
		if ($this->_location === NULL) {
			global $locations_manager;
			$location = $this->get_meta_field("location");
			if ($location) {
				$this->_location = $locations_manager->get($location);
			} else {
				$this->_location = false;
			}
		}

		return $this->_location;
	}

	public function get_destination () {
		if (!$this->_destination) {
			$destination = get_field("destination", $this->id);
			global $destinations_manager;
			$this->_destination = $destinations_manager->get($destination);
		}

		return $this->_destination;
	}

	public function get_camps () {
		$location = $this->get_location();
		if ($location) {
			return $location->get_camps();
		} else {
			return $this->get_destination()->get_camps();
		}

	}

}



global $packages_manager;
$packages_manager = new Packages_Manager();
