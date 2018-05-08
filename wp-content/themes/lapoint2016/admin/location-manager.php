<?php
/**
 * Manager class for Location post type
 *
 * @package WordPress
 * @subpackage Lapoint2016
 * @since Lapoint2016 1.0
 */

class Locations_Manager extends Lapoint_Manager {

	public $instance_class = Location;
	public $post_type = "location";
	public $url_struct = '%desttype%/%dest%/%postname%/%lang%';


	public function __construct () {
		parent::__construct();

		add_action('init', array($this, 'create_post_type'));
		add_action('acf/register_fields', array($this, 'register_acf_fields'));

		add_filter("manage_location_posts_columns", array($this, "change_columns"), 20);
		add_filter("manage_edit-location_sortable_columns", array($this, "sortable_columns"));


		add_filter('camp_rewrite_rules', array($this, 'add_permastruct'));
		add_filter('post_type_link', array($this, 'custom_post_permalink'), 10, 4);
	}


	# ****************************** Url ******************************
	public function add_permastruct( $rules ) {
		global $wp_rewrite;

	    // use the WP rewrite rule generating function
	    $rules = $wp_rewrite->generate_rewrite_rules(
	        $this->url_struct,       // the permalink structure
	        EP_PERMALINK,  // Endpoint mask: adds rewrite rules for single post endpoints like comments pages etc...
	        false,         // Paged: add rewrite rules for paging eg. for archives (not needed here)
	        true,          // Feed: add rewrite rules for feed endpoints
	        false,          // For comments: whether the feed rules should be for post comments - on a singular page adds endpoints for comments feed
	        false,         // Walk directories: whether to generate rules for each segment of the permastruct delimited by '/'. Always set to false otherwise custom rewrite rules will be too greedy, they appear at the top of the rules
	        true           // Add custom endpoints
	    );

	    return $rules;
	}


	public function custom_post_permalink( $permalink, $post, $leavename, $sample) {

	    if ($post->post_type == 'location' && get_option('permalink_structure')) {

	        $log = false;

	        if ($log) echo "!!!!! " . $permalink;

	        $rewritecodes = array(
	            '%desttype%',
	            '%dest%',
	            '%postname%',
	            '%lang%'
	        );


	        $desttype = '';
	        //if (strpos($permalink, '%destinationtype%') !== false) {

	        $destination = get_field("destination", $post->ID);

	        $dest = $destination->post_name;
	        $desttype = get_field("destination_type", $destination->ID)->post_name;

          if (empty($desttype)) {
              $desttype = "missing-data";
          }
          if (empty($dest)) {
              $dest = "missing-data";
          }

          // for local dev and staging WPML can be set to use query mode for translations to work.
					parse_str( parse_url( $permalink )["query"], $parsed_query );

	        $replacements = array(
	            $desttype,
	            $dest,
	            ($leavename) ? '%location%' : $post->post_name,
	            $parsed_query['lang'] ? '?lang=' . $parsed_query['lang'] : ''
	        );

	        // finish off the permalink
	        $permalink = get_wpml_home_url($permalink) . str_replace($rewritecodes, $replacements, $this->url_struct);

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
		return array_merge($custom_cols, $cols);
	}

	/*
	public function custom_columns ($column, $post_id) {
		switch ($column){
		    case "destination":
		    	$location = new Location($post_id);
		    	$destination = $location->get_destination();
		    	echo $destination ? $destination->title : "-";
		   		break;
	  	}
	}
	*/

	public function sortable_columns() {
		return array(
			'title' => 'title',
			'destination' => 'destination',
			'date' => 'date'
		);
	}

	# ****************************** Create Post Types ******************************
	public function create_post_type () {
		register_post_type('location',
			array(
				'labels' => array(
					'name' => __('Locations', 'lapoint'),
					'singular_name' => __('Location', 'lapoint'),
					'add_new' => __('Add location', 'lapoint'),
					'add_new_item' => __('Add new location', 'lapoint')
				),
				'public' => true,
				'has_archive' => true,
				'supports' => array('title', 'editor', 'thumbnail'),
				'menu_position' => 50,
        		'menu_icon' => 'dashicons-sticky'
			)
		);
	}




	# ****************************** Setup ACF ******************************
	public function register_acf_fields () {
		if (function_exists("register_field_group")) {
			register_field_group(array (
				'id' => 'acf_locations',
				'title' => 'Location Settings',
				'fields' => array (
					array (
						'key' => 'field_572889b2ece32',
						'label' => 'Destination',
						'name' => 'destination',
						'type' => 'post_object',
						'required' => 1,
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
						'key' => 'field_57288c547bd71',
						'label' => 'Display label',
						'name' => 'display_label',
						'type' => 'text',
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'html',
						'maxlength' => '',
					)
				),
				'location' => array (
					array (
						array (
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'location',
							'order_no' => 0,
							'group_no' => 0,
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
				'id' => 'acf_location-box',
				'title' => 'Location Box',
				'fields' => array (
					array (
						'key' => 'field_57288c547bf97',
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
						'key' => 'field_57288c547bfa2',
						'label' => 'Background image',
						'name' => 'box_background_image',
						'type' => 'image',
						'save_format' => 'object',
						'preview_size' => 'thumbnail',
						'library' => 'all',
					),
					array (
						'key' => 'field_57288c547bfab',
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
						'key' => 'field_57288c547bfb3',
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
						'key' => 'field_57288c5528419',
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
						'key' => 'field_57288c5528450',
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
					)
				),
				'location' => array (
					array (
						array (
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'location',
							'order_no' => 0,
							'group_no' => 0,
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
	}


	# ****************************** Getters ******************************
	public function get_all_by_destination ($destination_id) {
		return $this->get_all_by_meta_key("destination", $destination_id);
	}
}



class Location extends Lapoint_PostType {

	public function __construct ($post) {
		parent::__construct($post);

		$fields = $this->get_meta_fields();
		$this->display_label = $fields["display_label"];
	}

	public function get_camps () {
		if (!$this->_camps) {
			global $camps_manager;
			$this->_camps = $camps_manager->get_all_by_location($this->id);
		}
		return $this->_camps;
	}

	public function get_destination () {
		if (!$this->_destination) {
			global $destinations_manager;
			$destination = get_field("destination", $this->id);
			$this->_destination = $destinations_manager->get($destination);
		}

		return $this->_destination;
	}

	public function get_box_info () {
		if (!$this->box) {
			$fields = $this->get_meta_fields();

			$this->box = (object) array();
			$this->box->button_text = $fields["box_button_text"];
			$this->box->background_image = $fields["box_background_image"];
			$this->box->width_md = $fields["box_width_md"];
			$this->box->width_sm = $fields["box_width_sm"];
			$this->box->height_md = $fields["box_height_md"];
			$this->box->height_sm = $fields["box_height_sm"];
		}

		return $this->box;
	}


	public function get_packages () {
		if (!$this->_packages) {
			global $packages_manager;
			$this->_packages = $packages_manager->get_all_by_location($this->id);
		}
		return $this->_packages;
	}

}



global $locations_manager;
$locations_manager = new Locations_Manager();
