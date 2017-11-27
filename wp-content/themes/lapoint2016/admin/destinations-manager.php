<?php
/**
 * Manager class for Destination post type
 *
 * @package WordPress
 * @subpackage Lapoint2016
 * @since Lapoint2016 1.0
 */

class Destinations_Manager {

	public function __construct () {
		add_action('init', array($this, 'create_post_type'));
		add_action('acf/register_fields', array($this, 'register_acf_fields'));

		add_filter("manage_destination_posts_columns", array($this, "change_columns"), 20);
		//add_action("manage_posts_custom_column", array($this, "custom_columns"), 10, 2);
		add_filter("manage_edit-destination_sortable_columns", array($this, "sortable_columns"));

		add_filter('destination_rewrite_rules', array($this, 'add_permastruct'));
		add_filter('post_type_link', array($this, 'custom_post_permalink'), 10, 4);
	}


	# ****************************** URL ******************************
	public function add_permastruct( $rules ) {
		global $wp_rewrite;

	    // set your desired permalink structure here
	    $struct = '/%desttype%/%postname%/';

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
	    if ($post->post_type == 'destination' && get_option('permalink_structure')) {

	        // remember our desired permalink structure here
	        // we need to generate the equivalent with real data
	        // to match the rewrite rules set up from before

	        $struct = '%desttype%/%postname%/';

	        //echo "!!!!!" . $permalink;

	        $rewritecodes = array(
	            '%desttype%',
	            '%postname%'
	        );

        	$desttype = get_field("destination_type", $post->ID)->post_name;
            if (empty($desttype)) {
                $desttype = "missing-data";
            }

	        $replacements = array(
	            $desttype,
	            ($leavename) ? '%destination%' : $post->post_name
	        );

	        // finish off the permalink
	        $permalink = get_wpml_home_url($permalink) . str_replace($rewritecodes, $replacements, $struct);

	        $permalink = user_trailingslashit($permalink, 'single');

	        // echo " --> " . $permalink. "!!!!!!<br>";
	    }

	    return $permalink;
	}



/*
	add_filter('post_link', 'brand_permalink', 1, 3);
	add_filter('post_type_link', 'brand_permalink', 1, 3);

function brand_permalink($permalink, $post_id, $leavename) {
	//con %brand% catturo il rewrite del Custom Post Type
    if (strpos($permalink, '%brand%') === FALSE) return $permalink;
        // Get post
        $post = get_post($post_id);
        if (!$post) return $permalink;

        // Get taxonomy terms
        $terms = wp_get_object_terms($post->ID, 'brand');
        if (!is_wp_error($terms) && !empty($terms) && is_object($terms[0]))
        	$taxonomy_slug = $terms[0]->slug;
        else $taxonomy_slug = 'no-brand';

    return str_replace('%brand%', $taxonomy_slug, $permalink);
}*/


	# ****************************** Admin list ******************************
	public function change_columns( $cols ) {
		$custom_cols = array_slice($cols, 0, 2);
		$custom_cols["type"] = "Type";
		return array_merge($custom_cols, $cols);
	}
	public function sortable_columns() {
		return array(
			'title' => 'title',
			'type' => 'type',
			'date' => 'date'
		);
	}



	# ****************************** Create Post Types ******************************
	public function create_post_type () {
	    //add_rewrite_tag('%desttype%', '([^/]*)');

		register_post_type('destination',
			array(
				'labels' => array(
					'name' => __('Destinations', 'lapoint'),
					'singular_name' => __('Destination', 'lapoint'),
					'add_new' => __('Add destination', 'lapoint'),
					'add_new_item' => __('Add new destination', 'lapoint')
				),
				'public' => true,
				'publicly_queryable' => true,
				'has_archive' => true,
				'supports' => array('title', 'editor', 'thumbnail'),
				'menu_position' => 50,
        		'menu_icon' => 'dashicons-admin-site',
				//'taxonomies' => array('category'),
				//'rewrite'		=> array('slug' => '%category%', 'with_front' => false)
				//'rewrite'		=> array('slug' => '%destination_category%', 'with_front' => false),
				//'rewrite'		=> array('with_front' => false),
				//'rewrite' => array('slug' => 'desttype', 'with_front' => false),
				'rewrite' => false,
				'query_var'		=> true,
			)
		);

		/*
		$labels = array(
			'name'              => _x( 'Brand', 'taxonomy general name' ),
			'singular_name'     => _x( 'Brand', 'taxonomy singular name' ),
			'search_items'      => __( 'Search Product Categories' ),
			'all_items'         => __( 'All Product Categories' ),
			'parent_item'       => __( 'Parent Product Category' ),
			'parent_item_colon' => __( 'Parent Product Category:' ),
			'edit_item'         => __( 'Edit Product Category' ),
			'update_item'       => __( 'Update Product Category' ),
			'add_new_item'      => __( 'Add New Product Category' ),
			'new_item_name'     => __( 'New Product Category' ),
			'menu_name'         => __( 'Brand' ),
		);
		$args = array(
			'labels' => $labels,
			'hierarchical' 	=> true,
			'public'		=> true,
			'query_var'		=> 'destination_category',
			//slug prodotto deve coincidere con il primo parametro dello slug del Custom Post Type correlato
			//'rewrite'		=>  array('slug' => 'prodotto'),
			//'rewrite'		=>  array('with_front' => false),
			//'_builtin'		=> false,
		);
		register_taxonomy('destination_category', 'destination', $args );
		*/
	}



	# ****************************** Setup ACF ******************************
	public function register_acf_fields () {
		if (function_exists("register_field_group")) {
			register_field_group(array (
				'id' => 'acf_destination',
				'title' => 'Destination Settings',
				'fields' => array (
					array (
						'key' => 'field_56e54129bb67b',
						'label' => 'Type',
						'name' => 'destination_type',
						'type' => 'post_object',
						'required' => 1,
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
						'key' => 'field_5717f09ae3b8a',
						'label' => 'levels',
						'name' => 'levels',
						'type' => 'post_object',
						'instructions' => 'Select the levels that are available for the destination',
						'post_type' => array (
							0 => 'level',
						),
						'taxonomy' => array (
							0 => 'all',
						),
						'allow_null' => 0,
						'multiple' => 1,
					),
					array (
						'key' => 'field_571a8164ba398',
						'label' => 'Durations',
						'name' => 'durations',
						'type' => 'select',
						'instructions' => 'Select the durations that are available for the destination',
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
						'default_value' => '7
			14',
						'allow_null' => 0,
						'multiple' => 1,
					),
					array (
						'key' => 'field_56eac53f68a72',
						'label' => 'Booking code',
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
							'value' => 'destination',
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

		register_field_group(array (
			'id' => 'acf_destination-box',
			'title' => 'Destination Box',
			'fields' => array (
				array (
					'key' => 'field_56e91e85ad332',
					'label' => 'Background image',
					'name' => 'box_background_image',
					'type' => 'image',
					'save_format' => 'object',
					'preview_size' => 'thumbnail',
					'library' => 'all',
				),
				array (
					'key' => 'field_56e91e9cad333',
					'label' => 'Box columns, large',
					'name' => 'box_width_md',
					'type' => 'select',
					'instructions' => 'How many columns should the box span when the browser width is larger than 992px.',
					'choices' => array (
						1 => '1 column',
						2 => '2 columns',
					),
					'default_value' => '',
					'allow_null' => 0,
					'multiple' => 0,
				),
				array (
					'key' => 'field_57023e8ebf500',
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
					'key' => 'field_56e91ed3ad334',
					'label' => 'Box columns, medium',
					'name' => 'box_width_sm',
					'type' => 'select',
					'instructions' => 'How many columns should the box span when the browser width is between 768px and 992px.',
					'choices' => array (
						1 => '1 column',
						2 => '2 columns',
					),
					'default_value' => '',
					'allow_null' => 0,
					'multiple' => 0,
				),
				array (
					'key' => 'field_57023e8ebf4f5',
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
				/*
				array (
					'key' => 'field_56e91ef9c1bb8',
					'label' => 'Info header',
					'name' => 'box_header',
					'type' => 'text',
					'instructions' => 'The title displayed when hovering the box',
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'formatting' => 'html',
					'maxlength' => '',
				),
				array (
					'key' => 'field_56e91f27c1bb9',
					'label' => 'Info body',
					'name' => 'box_body',
					'type' => 'textarea',
					'instructions' => 'The info text displayed when hovering the box',
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'formatting' => 'html',
					'maxlength' => '',
				),
				*/
				array (
					'key' => 'field_56e91e5fad331',
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
			),
			'location' => array (
				array (
					array (
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'destination',
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





	# ****************************** Getters ******************************
	public function get ($data) {
		if (gettype($data) == "object") {
			return new Destination($data);
		} else {
			$post = get_post($data);
			return new Destination($post);
		}
	}

	public function get_all () {
		if (!$this->_all) {
			global $wpdb;
			$posts = $wpdb->get_results(sprintf("
				SELECT wp.ID, wp.post_title
				FROM %s AS wp
				INNER JOIN %s as wpml ON wp.ID=wpml.element_id
				WHERE wp.post_type='destination' AND wp.post_status='publish' AND wpml.language_code='%s'",
				$wpdb->posts, $wpdb->prefix . "icl_translations", ICL_LANGUAGE_CODE)
			);

			$this->_all = array_map(function ($post) {
				return new Destination($post);
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
				WHERE wp.post_type='destination' AND wp.post_status='publish' AND wpml.language_code='%s' ORDER BY wp.menu_order",
				$wpdb->posts, $wpdb->prefix . "icl_translations", $lang)
			);

			$this->_all = array_map(function ($post) {
				return new Destination($post);
			}, $posts);
		}
		return $this->_all;
	}

	public function get_all_by_type ($type_id) {
		global $wpdb;
		$posts = $wpdb->get_results(sprintf("
			SELECT wp.ID, wp.post_title
			FROM %s AS wp
			INNER JOIN %s as wpml ON wp.ID=wpml.element_id
			INNER JOIN %s AS wpmeta ON wpmeta.post_id=wp.ID AND wpmeta.meta_key='destination_type' AND wpmeta.meta_value='%d'
			WHERE wp.post_type='destination' AND wp.post_status='publish' AND wpml.language_code='%s' ORDER BY wp.menu_order",
			$wpdb->posts, $wpdb->prefix . "icl_translations", $wpdb->postmeta, $type_id, ICL_LANGUAGE_CODE)
		);

		return array_map(function ($post) {
			return new Destination($post);
		}, $posts);
	}

	public function get_all_all_lang () {
		if (!$this->_all_all_lang) {

			$posts = get_posts(array(
				'posts_per_page' => -1,
				'post_type' => 'destination',
				'orderby' => 'menu_order',
				'order' => 'ASC',
				'post_status' => 'publish',
				'suppress_filters' => 1
			));

			$this->_all_all_lang = array_map(function ($post) {
				return new Destination($post);
			}, $posts);
		}

		return $this->_all_all_lang;
	}
}


class Destination extends Lapoint_PostType {

	public function __construct ($post) {
		parent::__construct($post);

		$fields = $this->get_meta_fields();
		$this->booking_code = $fields["booking_code"];
		$this->levels = $fields["levels"];
		$this->durations = $fields["durations"];
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

	public function get_package_for_level ($level) {
		global $packages_manager;
		$packages = $packages_manager->get_by_destination_and_level($this->id, $level);
		if (count($packages) > 0) {
			return $packages[0];
		} else {
			return false;
		}
	}

	public function get_packages () {
		if (!$this->_packages) {
			global $packages_manager;
			$this->_packages = $packages_manager->get_all_by_destination($this->id);
		}
		return $this->_packages;
	}

	public function get_camps () {
		if (!$this->_camps) {
			global $camps_manager;
			$this->_camps = $camps_manager->get_all_by_destination($this->id);
		}
		return $this->_camps;
	}

	public function get_locations () {
		if (!$this->_locations) {
			global $locations_manager;
			$this->_locations = $locations_manager->get_all_by_destination($this->id);
		}
		return $this->_locations;
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

}



global $destinations_manager;
$destinations_manager = new Destinations_Manager();
