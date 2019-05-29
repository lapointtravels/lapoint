<?php
/**
 * Manager class for Camp post type
 *
 * @package WordPress
 * @subpackage Lapoint2016
 * @since Lapoint2016 1.0
 */

class Camps_Manager extends Lapoint_Manager {

	public $instance_class = "Camp";
	public $post_type = "camp";

	public function __construct () {
		parent::__construct();

		add_action('init', array($this, 'create_post_type'));
		add_action('acf/register_fields', array($this, 'register_acf_fields'));

		add_filter("manage_camp_posts_columns", array($this, "change_columns"), 20);
		add_filter("manage_edit-camp_sortable_columns", array($this, "sortable_columns"));

		add_filter('camp_rewrite_rules', array($this, 'add_permastruct'));
		add_filter('post_type_link', array($this, 'custom_post_permalink'), 10, 4);
	}


	# ****************************** URL ******************************
	public function add_permastruct( $rules ) {
		global $wp_rewrite;

	    // set your desired permalink structure here
	    $struct = '/%desttype%/%dest%/%loccamps%/%postname%/';

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


	public function custom_post_permalink( $permalink, $post, $leavename, $sample) {

	    // only do our stuff if we're using pretty permalinks
	    // and if it's our target post type
	    if ($post->post_type == 'camp' && get_option('permalink_structure')) {
	        // remember our desired permalink structure here
	        // we need to generate the equivalent with real data
	        // to match the rewrite rules set up from before

	        $struct = '%desttype%/%dest%/%loccamps%/%postname%/%lang%';

	        $log = false;

	        if ($log) echo "<br><br>!!!!!" . $permalink;

	        $rewritecodes = array(
	            '%desttype%',
	            '%dest%',
	            '%loccamps%',
	            '%postname%',
	            '%lang%'
	        );

	        // for local dev and staging WPML can be set to use query mode for translations to work.
					parse_str( parse_url( $permalink )["query"], $parsed_query );

					// for hardcoded loc_camps below when ruuning in query moode
					$loc_camp_lang = $parsed_query['lang'] ? $parsed_query['lang'] : false;


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

          // Append location if the camp has one
	        $location = get_field("location", $post->ID);
          if ($location) {
          	$dest .= "/" . $location->post_name;
          }

          $loc_camps = "accommodations";
            
	        // $language_code = "";
	        if (strpos($permalink, "lapoint.dk") || $loc_camp_lang == "da") {
	        	$loc_camps = "indkvartering";
	        	// $language_code = "/da";
	        } else if (strpos($permalink, "lapoint.no") || $loc_camp_lang == "nb") {
	        	// $language_code = "/nb";
	        	$loc_camps = "overnatting";
	        } else if (strpos($permalink, "lapoint.se") || $loc_camp_lang == "sv") {
	        	// $language_code = "/sv";
	        	$loc_camps = "boenden";
	        }

	        $replacements = array(
	            $desttype,
	            $dest,
	            $loc_camps,
	            ($leavename) ? '%camp%' : $post->post_name,
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
		return array_merge($custom_cols, $cols);
	}
	public function sortable_columns() {
		return array(
			'title' => 'title',
			'destination' => 'destination',
			'location' => 'location',
			'date' => 'date'
		);
	}



	# ****************************** Create Post Types ******************************
	public function create_post_type () {
		register_post_type('camp',
			array(
				'labels' => array(
					'name' => __('Camps', 'lapoint'),
					'singular_name' => __('Camp', 'lapoint'),
					'add_new' => __('Add camp', 'lapoint'),
					'add_new_item' => __('Add new camp', 'lapoint')
				),
				'public' => true,
				'publicly_queryable' => true,
				'has_archive' => true,
				'supports' => array('title', 'editor', 'thumbnail'),
				'menu_position' => 51,
        		'menu_icon' => 'dashicons-palmtree',
				//'rewrite' => true,
				'rewrite' => false,
				'query_var' => true,
			)
		);
	}




	# ****************************** Setup ACF ******************************
	public function register_acf_fields () {
		if (function_exists("register_field_group")) {
			register_field_group(array (
				'id' => 'acf_camps',
				'title' => 'Camps Settings',
				'fields' => array (
					array (
						'key' => 'field_56dd8b6c2f481',
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
						'key' => 'field_5710cb3044e81',
						'label' => 'Camp location',
						'name' => 'location',
						'type' => 'post_object',
						'instructions' => 'Optional. Select a location to be able to group camps to use with the Location camps component',
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
						'key' => 'field_34ba0618160c9',
						'label' => 'Default search duration',
						'name' => 'search_duration',
						'type' => 'select',
						'instructions' => 'Select the default duration that will be used for searching when this camp is selected in the booking bar',
						'choices' => array (
							'WE' => __("Weekend", "lapoint"),
							1 => __("1 day", "lapoint"),
							2 => __("2 days", "lapoint"),
							3 => __("3 days", "lapoint"),
							4 => __("4 days", "lapoint"),
							5 => __("5 days", "lapoint"),
							6 => __("6 days", "lapoint"),
							7 => __("1 week", "lapoint"),
							7 => __("1 week", "lapoint"),
							14 => __("2 week", "lapoint"),
							21 => __("3 week", "lapoint")
						),
						'default_value' => '7',
						'allow_null' => 0,
						'multiple' => 0,
					),
					array(
						'key' => 'field_4975926ab8167',
						'label' => 'Levels',
						'name' => 'levels',
						'type' => 'post_object',
						'instructions' => 'Select the levels that are available for the camp. Select none to use the levels available for the destination.',
						'post_type' => array (
							0 => 'level',
						),
						'taxonomy' => array (
							0 => 'all',
						),
						'allow_null' => 1,
						'multiple' => 1
					),
					array (
						'key' => 'field_56eacc5d3779f',
						'label' => 'Booking code',
						'name' => 'booking_code',
						'type' => 'text',
						'instructions' => 'The code used when talking to the Travelize booking system. Each product id at Travelize consists of four different parts, combined from destination type, destination, camp and level. Like this: [Destination type code]_[Destination code]_[Camp code]_[Level code]. If multiple camps share the same booking code, only the first one will be displayed',
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'html',
						'maxlength' => '',
					),
					array (
						'key' => 'field_571a3408a133c',
						'label' => 'Box image',
						'name' => 'box_background_image',
						'type' => 'image',
						'save_format' => 'object',
						'preview_size' => 'thumbnail',
						'library' => 'all',
					),
					array (
						'key' => 'field_572b9de2c0b0f',
						'label' => 'Booking label',
						'name' => 'booking_label',
						'type' => 'text',
						'instructions' => '(Optional) Override the label to be displayed in the booking bar dropdown',
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'html',
						'maxlength' => '',
					),
					array (
						'key' => 'field_56f19c0e5b26b',
						'label' => 'Excerpt',
						'name' => 'excerpt',
						'type' => 'textarea',
						'instructions' => 'The excerpt displayed in Camp rows component',
						'default_value' => '',
						'placeholder' => '',
						'maxlength' => '',
						'rows' => '',
						'formatting' => 'br',
					),
					array (
						'key' => 'field_56f19c335b26c',
						'label' => 'Info list',
						'name' => 'info_list',
						'type' => 'textarea',
						'instructions' => 'Info section displayed in Camp rows component',
						'default_value' => '',
						'placeholder' => '',
						'maxlength' => '',
						'rows' => '',
						'formatting' => 'br',
					),
					array (
						'key' => 'field_56f19d0b98337',
						'label' => 'Button text',
						'name' => 'button_text',
						'type' => 'text',
						'instructions' => 'Text for the button displayed in Camp rows component',
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'html',
						'maxlength' => '',
					),
					array (
						'key' => 'field_56f19d0e911b3',
						'label' => 'Hide under accommodations',
						'name' => 'hide_accommodation',
						'type' => 'true_false',
						'instructions' => 'If you want to hide this camp in the accommodations listing check the box below',
						'default_value' => 0,
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'html',
						'maxlength' => '',
						'message' => 'Hide me',
					),
				),
				'location' => array (
					array (
						array (
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'camp',
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
		}
	}



	# ****************************** Getters ******************************
	public function get_all_by_destination ($destination_id) {
		return $this->get_all_by_meta_key("destination", $destination_id);
	}
	public function get_all_by_location ($location_id) {
		return $this->get_all_by_meta_key("location", $location_id);
	}
}



class Camp extends Lapoint_PostType {

	public function __construct ($post) {
		parent::__construct($post);

		$fields = $this->get_meta_fields();

		$this->booking_code = $fields["booking_code"];
		$this->booking_label = $fields["booking_label"];
		$this->search_duration = $fields["search_duration"];
		$this->excerpt = $fields["excerpt"];
		$this->info_list = $fields["info_list"];
		$this->button_text = $fields["button_text"];
		$this->box_background_image = $fields["box_background_image"];
		$this->hide_in_accommodation_list = $fields["hide_accommodation"];
		$this->levels = $fields["levels"];
	}

	public function get_type () {
		if (!$this->_type) {
			$destination = $this->get_destination();
			$this->_type = $destination->get_type();
		}

		return $this->_type;
	}

	public function get_destination () {
		if (!$this->_destination) {
			global $destinations_manager;
			$destination = $this->get_meta_field("destination");
			$this->_destination = $destinations_manager->get($destination);
		}

		return $this->_destination;
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


	public function get_packages () {
		$location = $this->get_location();
		if ($location) {
			return $location->get_packages();
		} else {
			return $this->get_destination()->get_packages();
		}
	}

}



global $camps_manager;
$camps_manager = new Camps_Manager();
