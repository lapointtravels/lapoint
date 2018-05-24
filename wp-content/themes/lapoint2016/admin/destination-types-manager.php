<?php
/**
 * Manager class for Destination types post type
 *
 * @package WordPress
 * @subpackage Lapoint2016
 * @since Lapoint2016 1.0
 */

class Destinations_Types_Manager {

	public function __construct () {
		add_action('init', array($this, 'create_post_type'));

		add_action('acf/register_fields', array($this, 'register_acf_fields'));

		//add_filter( 'post_type_link', array($this, 'custom_remove_cpt_slug'), 10, 3 );
		//add_action( 'pre_get_posts', array($this, 'custom_parse_request_tricksy') );



		add_filter('package_rewrite_rules', array($this, 'add_permastruct'));
		add_filter('post_type_link', array($this, 'custom_post_permalink'), 10, 4);
	}




	public function add_permastruct( $rules ) {
		global $wp_rewrite;

	    // set your desired permalink structure here
	    $struct = '/%postname%/';

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
	    if ($post->post_type == 'destination-type' && get_option('permalink_structure')) {
	        // remember our desired permalink structure here
	        // we need to generate the equivalent with real data
	        // to match the rewrite rules set up from before

	        $struct = '%postname%/%lang%';

	        $log = false;

	        if ($log) echo "!!!!! " . $permalink . " | ";

	        $rewritecodes = array(
	            '%postname%',
	            '%lang%'
	        );

	        // for local dev and staging WPML can be set to use query mode for translations to work.
					parse_str( parse_url( $permalink )["query"], $parsed_query );

	        $replacements = array(
	            ($leavename) ? '%destination-type%' : $post->post_name,
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


	# ****************************** Create Post Types ******************************
	public function create_post_type () {
		register_post_type('destination-type',
			array(
				'labels' => array(
					'name' => __('Destination Types', 'lapoint'),
					'singular_name' => __('Destination Type', 'lapoint'),
					'add_new' => __('Add destination type', 'lapoint'),
					'add_new_item' => __('Add new destination type', 'lapoint')
				),
				'public' => true,
				'supports' => array('title', 'editor', 'thumbnail'),
				'menu_position' => 49,
        		'menu_icon' => 'dashicons-carrot',
        		/*,
        		'rewrite' => array(
	                'slug' => 'destination-type',
	                'with_front' => false
				)*/
				'publicly_queryable' => true,
				'rewrite' => false,
				'query_var' => true,
			)
		);
	}


	/**
	 * Remove the slug from published post permalinks.
	 */
	/*
	function custom_remove_cpt_slug( $post_link, $post, $leavename ) {

	    if ( 'destination-type' != $post->post_type || 'publish' != $post->post_status ) {
	        return $post_link;
	    }

	    $post_link = str_replace( '/' . $post->post_type . '/', '/', $post_link );

	    return $post_link;
	}*/
	/**
	 * Some hackery to have WordPress match postname to any of our public post types
	 * All of our public post types can have /post-name/ as the slug, so they better be unique across all posts
	 * Typically core only accounts for posts and pages where the slug is /post-name/
	 */
	/*
	function custom_parse_request_tricksy( $query ) {
		echo "<br>";
		echo "Name:" . $query->query['name'] . "... <br>";
		echo count( $query->query ) . "... <br>";
		echo $query->query['page'] . "... <br>";
		echo "<br>";
	    // Only noop the main query
	    if ( ! $query->is_main_query() )
	        return;

	    // Only noop our very specific rewrite rule match
	    if ( 2 != count( $query->query ) || ! isset( $query->query['page'] ) ) {
	        return;
	    }

	    // 'name' will be set if post permalinks are just post_name, otherwise the page rule will match
	    if ( ! empty( $query->query['name'] ) ) {

	        $query->set( 'post_type', array( 'post', 'destination-type', 'page' ) );
	    }
	}
	*/


	# ****************************** Setup ACF ******************************
	public function register_acf_fields () {
		if (function_exists("register_field_group")) {
			register_field_group(array (
				'id' => 'acf_destination-type-settings',
				'title' => 'Destination Type Settings',
				'fields' => array (
					array (
						'key' => 'field_56e4a6ce40a14',
						'label' => 'Front page',
						'name' => 'front_page',
						'type' => 'true_false',
						'instructions' => 'Use this destination type as the default on the home page?',
						'message' => '',
						'default_value' => 0,
					),
					array (
						'key' => 'field_56ea70312cb66',
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
							'value' => 'destination-type',
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
	public function get ($data) {
		if (gettype($data) == "object") {
			return new Destination_Type($data);
		} else {
			$post = get_post($data);
			return new Destination_Type($post);
		}
	}

	public function get_all () {
		if (!$this->_all) {
			global $wpdb;
			$posts = $wpdb->get_results(sprintf("
				SELECT wp.ID, wp.post_title
				FROM %s AS wp
				INNER JOIN %s as wpml ON wp.ID=wpml.element_id
				WHERE wp.post_type='destination-type' AND wp.post_status='publish' AND wpml.language_code='%s' ORDER BY wp.menu_order",
				$wpdb->posts, $wpdb->prefix . "icl_translations", ICL_LANGUAGE_CODE)
			);


			$this->_all = array_map(function ($post) {
				return new Destination_Type($post);
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
				WHERE wp.post_type='destination-type' AND wp.post_status='publish' AND wpml.language_code='%s' ORDER BY wp.menu_order",
				$wpdb->posts, $wpdb->prefix . "icl_translations", $lang)
			);


			$this->_all = array_map(function ($post) {
				return new Destination_Type($post);
			}, $posts);
		}

		return $this->_all;
	}



	public function get_all_all_lang () {
		if (!$this->_all_all_lang) {

			$posts = get_posts(array(
				'posts_per_page' => -1,
				'post_type' => 'destination-type',
				'orderby' => 'menu_order',
				'order' => 'ASC',
				'post_status' => 'publish',
				'suppress_filters' => 1
			));

			$this->_all_all_lang = array_map(function ($post) {
				return new Destination_Type($post);
			}, $posts);
		}

		return $this->_all_all_lang;
	}
}


class Destination_Type extends Lapoint_PostType {

	public function __construct ($post) {
		parent::__construct($post);
		//$this->id = $post->ID;
		//$this->title = $post->post_title;
		//$this->content = $post->post_content;
		//$this->name = $post->post_name;
		//$this->link = get_permalink($this->id);

  		$fields = $this->get_meta_fields();
  		$this->booking_code = $fields["booking_code"];
	}

	public function get_levels () {
		if (!$this->_levels) {
			global $levels_manager;
			$this->_levels = $levels_manager->get_all_visible_by_type($this->id);
		}
		return $this->_levels;
	}

	public function get_destinations () {
		if (!$this->_destinations) {
			global $destinations_manager;
			$this->_destinations = $destinations_manager->get_all_by_type($this->id);
		}
		return $this->_destinations;
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

	/*
	public function get_link () {
		if (!$this->_link) {
			$this->_link = get_permalink($this->id);
		}
		return $this->_link;
	}
	*/

}


global $destination_types_manager;
$destination_types_manager = new Destinations_Types_Manager();
