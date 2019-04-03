<?php
/**
 * Manager class for Level post type
 *
 * @package WordPress
 * @subpackage Lapoint2016
 * @since Lapoint2016 1.0
 */

class Levels_Manager extends Lapoint_Manager {

	public $instance_class = "Level";
	public $post_type = "level";


	public function __construct () {
		add_action('init', array($this, 'create_post_type'));
		add_action('acf/register_fields', array($this, 'register_acf_fields'));

		add_filter('package_rewrite_rules', array($this, 'add_permastruct'));
		add_filter('post_type_link', array($this, 'custom_post_permalink'), 10, 4);

	}



	public function add_permastruct( $rules ) {
		global $wp_rewrite;

	    // set your desired permalink structure here
	    $struct = '/%desttype%/levels/%postname%/';

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


	function custom_post_permalink( $permalink, $post, $leavename, $sample ) {

	    // only do our stuff if we're using pretty permalinks
	    // and if it's our target post type
	    if ($post->post_type == 'level' && get_option('permalink_structure')) {
	        // remember our desired permalink structure here
	        // we need to generate the equivalent with real data
	        // to match the rewrite rules set up from before

	        $struct = '%desttype%/levels/%postname%/%lang%';

	        $log = false;

	        if ($log) echo "!!!!!" . $permalink;

	        $rewritecodes = array(
	            '%desttype%',
	            '%postname%',
	            '%lang%'
	        );

	        // for local dev and staging WPML can be set to use query mode for translations to work.
					parse_str( parse_url( $permalink )["query"], $parsed_query );

	        $desttype = get_field("destination_type", $post->ID)->post_name;
          if (empty($desttype)) {
              $desttype = "missing-data";
          }

	        $replacements = array(
	            $desttype,
	            ($leavename) ? '%level%' : $post->post_name,
	            $parsed_query['lang'] ? '?lang=' . $parsed_query['lang'] : ''
	        );

	        // finish off the permalink
	        // $permalink = site_url() . $language_code . str_replace($rewritecodes, $replacements, $struct);
	        $permalink = get_wpml_home_url($permalink) . str_replace($rewritecodes, $replacements, $struct);
	        //$permalink = home_url( $language_code . str_replace( $rewritecodes, $replacements, $struct ) );
	        if( !$parsed_query['lang'] ) {
		        $permalink = user_trailingslashit($permalink, 'single');	        	
	        }

	        if ($log) echo " --> " . $permalink. "!!!!!!<br>";
	    }

	    return $permalink;
	}


	# ****************************** Create Post Types ******************************
	public function create_post_type () {
		register_post_type('level',
			array(
				'labels' => array(
					'name' => __('Levels', 'lapoint'),
					'singular_name' => __('Level', 'lapoint'),
					'add_new' => __('Add Level', 'lapoint'),
					'add_new_item' => __('Add new Level', 'lapoint')
				),
				'public' => true,
				'has_archive' => true,
				'supports' => array('title', 'editor', 'thumbnail'),
				'menu_position' => 52,
        		'menu_icon' => 'dashicons-sos',
    			//'rewrite' => array( 'slug' => 'level' )
				'publicly_queryable' => true,
				'rewrite' => false,
				'query_var' => true,
			)
		);

		//flush_rewrite_rules();
	}


	# ****************************** Setup ACF ******************************
	public function register_acf_fields () {
		if (function_exists("register_field_group")) {

			register_field_group(array (
				'id' => 'acf_levels-destination-type',
				'title' => 'Levels - Settings',
				'fields' => array (
					array (
						'key' => 'field_57109da2c05e2',
						'label' => 'Parent Level',
						'name' => 'parent_level',
						'type' => 'post_object',
						'instructions' => 'Select parent level if this level is a sub level.',
						'post_type' => array (
							0 => 'level',
						),
						'taxonomy' => array (
							0 => 'all',
						),
						'allow_null' => 1,
						'multiple' => 0,
					),
					array (
						'key' => 'field_56e80b243d2b1',
						'label' => 'Destination type',
						'name' => 'destination_type',
						'type' => 'post_object',
						'post_type' => array (
							0 => 'destination-type',
						),
						'taxonomy' => array (
							0 => 'all',
						),
						'allow_null' => 0,
						'multiple' => 0,
					),
					array (
						'key' => 'field_56f3cf2482818',
						'label' => 'Display label',
						'name' => 'display_label',
						'type' => 'text',
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'html',
						'maxlength' => '',
					),
					array (
						'key' => 'field_57133a65df7bb',
						'label' => 'Hide in menu',
						'name' => 'hidden_menu',
						'type' => 'true_false',
						'instructions' => 'Check this to remove the level from the menu and levels component',
						'message' => '',
						'default_value' => 0,
					),
					array (
						'key' => 'field_56eacc3c39d3b',
						'label' => 'Booking Code',
						'name' => 'booking_code',
						'type' => 'text',
						'instructions' => 'The code used when talking to the Travelize booking system. Each product id at Travelize consists of four different parts, combined from destination type, destination, camp and level. Like this: [Destination type code]_[Destination code]_[Camp code]_[Level code]',
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'html',
						'maxlength' => '',
					),
				),
				'location' => array (
					array (
						array (
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'level',
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
				'id' => 'acf_level-box',
				'title' => 'Level Box',
				'fields' => array (
					array (
						'key' => 'field_56e675292305a',
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
						'key' => 'field_56e6769ee0ba4',
						'label' => 'Background image',
						'name' => 'box_background_image',
						'type' => 'image',
						'save_format' => 'object',
						'preview_size' => 'thumbnail',
						'library' => 'all',
					),
					array (
						'key' => 'field_56e675572305b',
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
						'key' => 'field_57023e8ebf511',
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
						'key' => 'field_56e676682305c',
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
						'key' => 'field_57023e8ebf508',
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
							'value' => 'level',
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
	public function get_all_by_type ($type_id) {
		return $this->get_all_by_meta_key_without_meta("destination_type", $type_id, "parent_level");
	}
	public function get_all_visible_by_type ($type_id) {
		return $this->get_all_by_meta_key_without_metas("destination_type", $type_id, "parent_level", "hidden_menu");
	}
	public function get_sub_levels ($level_id) {
		return $this->get_all_by_meta_key("parent_level", $level_id);
	}

}



class Level extends Lapoint_PostType {

	public function __construct ($post) {
		parent::__construct($post);

		$fields = $this->get_meta_fields();
		$this->booking_code = $fields["booking_code"];
		$this->display_label = $fields["display_label"];
		$this->parent_level = $fields["parent_level"];

		if (!$this->display_label) {
			$this->display_label = $this->title;
		}
	}


	public function get_type () {
		if (!$this->_type) {
			$fields = $this->get_meta_fields();
			$type = $fields["destination_type"];
			global $destination_types_manager;
			$this->_type = $destination_types_manager->get($type);
		}

		return $this->_type;
	}

	public function get_levels () {
		if (!$this->_levels) {
			global $levels_manager;
			$this->_levels = $levels_manager->get_sub_levels($this->id);
		}

		return $this->_levels;
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

}



global $levels_manager;
$levels_manager = new Levels_Manager();
