<?php
/**
 * The functions file required by Wordpress
 *
 * @package WordPress
 * @subpackage Lapoint2016
 * @since Lapoint2016 1.0
 */



define('HOME_URI', home_url());
define('THEME_URL', get_stylesheet_directory_uri());
define('THEME_DIR', dirname(__FILE__));
define('ADMIN_DIR', THEME_DIR .'/admin');
define('WPML_HOME_URI', apply_filters( 'wpml_home_url', get_option('home')));

define('ICL_DONT_LOAD_NAVIGATION_CSS', true);
define('ICL_DONT_LOAD_LANGUAGE_SELECTOR_CSS', true);
define('ICL_DONT_LOAD_LANGUAGES_JS', true);

if (!isset($content_width)) {
	$content_width = 1170;
}


require_once(ADMIN_DIR .'/abstract-post-type.php');
require_once(ADMIN_DIR .'/destination-types-manager.php');
require_once(ADMIN_DIR .'/destinations-manager.php');
require_once(ADMIN_DIR .'/location-manager.php');
require_once(ADMIN_DIR .'/camps-manager.php');
require_once(ADMIN_DIR .'/packages-manager.php');
require_once(ADMIN_DIR .'/levels-manager.php');
require_once(ADMIN_DIR .'/videos-manager.php');
require_once(ADMIN_DIR .'/imageslider-settings.php');
require_once(ADMIN_DIR .'/helper-functions.php');


/**
 * Main Framework Class
 *
 * @since 1.0.0
 * @package Lapoint2016
 */
class Lapoint_Framework {

	public $version = "1.1.3";

	/**
	 * Constructor - Setup hooks and filters
	 * @since Lapoint2016 1.0
	 */
	public function __construct ($options = array()) {

		//exit("Site transfer is in progress...");

		add_action('after_setup_theme', array($this, 'after_setup'));
		add_action('widgets_init', array($this, 'widgets_init'));
		add_action('init', array($this, 'add_tinymce_buttons'));
		add_action('init', array($this, 'init'));
		add_action('admin_menu', array($this, 'admin_menu'));

		add_action('admin_head', array($this, 'admin_menu_fix'));
		add_action('wp_enqueue_scripts', array($this, 'lapoint_scripts'));
		add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));

		add_action('acf/register_fields', array($this, 'register_acf_fields'));
		add_action('kcm/register_modules', array($this, 'register_kcm_modules'));

		//add_filter('show_admin_bar', '__return_false');
		add_filter('wp_revisions_to_keep', array($this, 'set_wp_revision_count'), 10, 2);
		add_filter('kmc/settings', array($this, 'set_kmc_settings'), 10, 3);

		add_filter('kmc/register_admin_types', array($this, 'set_kmc_admin_types'), 10, 3);
		add_filter('kmc/register_categories', array($this, 'set_kmc_categories'), 10, 3);
		add_filter('kmc/set_kmc_edit', array($this, 'set_kmc_edit'), 10, 3);

		add_filter('kloonslides/settings', array($this, 'set_kloonslides_settings'), 10, 3);
		add_filter('imageslider_settings', array($this, 'set_imageslider_settings'), 10, 3);

		add_filter('excerpt_more', array($this, 'new_excerpt_more'));
		add_filter('excerpt_length', array($this, 'custom_excerpt_length'), 999);

		// Setup home to show a given Destination Type
		add_action('pre_get_posts', array($this, 'modify_main_query'));
		add_filter('template_include', array($this, 'modify_home_template'), 99);
		add_filter('amt_get_queried_object', array($this, 'amt_get_queried_object'), 99);

		add_action('wp', array($this, 'setup_theme_data'));

		add_filter('acf/fields/post_object/result/name=destination', array($this, 'acf_destination_field'), 10, 3);

		//flush_rewrite_rules();
		add_action('init', array($this, 'add_rewrite_rules'), 5);

		add_action("manage_posts_custom_column", array($this, "custom_columns"), 10, 2);

		//add_action('parse_request', array ($this, 'sniff_requests'), 0);
		//add_filter('package_rewrite_rules', array($this, 'add_permastruct'));

		//add_filter('post_rewrite_rules', array($this, 'add_permastruct'));
		//add_filter('post_link', array($this, 'custom_post_permalink'), 10, 2);


		add_action('wp_ajax_update_booking_code', array($this, 'ajax_update_booking_code'));
		add_action('wp_ajax_nopriv_fetch_packages', array($this, 'ajax_fetch_packages'));
		add_action('wp_ajax_fetch_packages', array($this, 'ajax_fetch_packages'));

		add_filter('pre_get_document_title', array($this, 'change_the_title'));

		remove_action('wp_head', 'rel_canonical');

		add_shortcode('destination_box', array($this, 'render_destination_box'));

		// create 
		add_action('admin_menu', array($this, 'top_banner_bar_create_menu'));

	}

	// Admin menu page for top banner bar text
	// Output is rendered in header.php
	public function top_banner_bar_create_menu() {
		
	 add_menu_page(
        'Top Banner Bar',
        'Top Banner Bar',
        'manage_options',
        'top_banner_bar',
        array($this, 'top_banner_bar_page'),
        'dashicons-archive',
        60
  	);

	 add_action( 'admin_init', array( $this, 'register_top_banner_bar_settings') );
		
	}

	public function register_top_banner_bar_settings() {
		register_setting( 'top-banner-bar-settings-group', 'tbb_show_banner', array(
			'type' => 'boolean'
		));
		register_setting( 'top-banner-bar-settings-group', 'tbb_banner_text_en', array(
			'type' => 'string'
		));
		register_setting( 'top-banner-bar-settings-group', 'tbb_banner_text_se', array(
			'type' => 'string'
		));
		register_setting( 'top-banner-bar-settings-group', 'tbb_banner_text_no', array(
			'type' => 'string'
		));
		register_setting( 'top-banner-bar-settings-group', 'tbb_banner_text_dk', array(
			'type' => 'string'
		));
	}

	public function top_banner_bar_page () {
		?>

		<div class="wrap">
		<h1>Top Banner Bar</h1>

		<form method="post" action="options.php">
		    <?php settings_fields( 'top-banner-bar-settings-group' ); ?>
		    <?php do_settings_sections( 'top-banner-bar-settings-group' ); ?>

		    <div>
		    	<h4>Show Top Bar Banner</h4>
		    	<input type="checkbox" name="tbb_show_banner" value="1" <?php checked(1, get_option('tbb_show_banner'), true); ?> /> 
		    </div>

		    <div>
		    	<h4>English Banner Text</h4>
		    	<input type="text" name="tbb_banner_text_en" value="<?php echo esc_attr( get_option('tbb_banner_text_en') ); ?>" style="width: 100%;" />
		    </div>

		    <div>
		    	<h4>Swedish Banner Text</h4>
		    	<input type="text" name="tbb_banner_text_se" value="<?php echo esc_attr( get_option('tbb_banner_text_se') ); ?>" style="width: 100%;" />
		    </div>

		    <div>
		    	<h4>Norwegian Banner Text</h4>
		    	<input type="text" name="tbb_banner_text_no" value="<?php echo esc_attr( get_option('tbb_banner_text_no') ); ?>" style="width: 100%;" />
		    </div>

		    <div>
		    	<h4>Danish Banner Text</h4>
		    	<input type="text" name="tbb_banner_text_dk" value="<?php echo esc_attr( get_option('tbb_banner_text_dk') ); ?>" style="width: 100%;" />
		    </div>
		    
		    <?php submit_button(); ?>

		</form>
		</div>

		<?php
	}

	public function set_kloonslides_settings ($settings) {
		// Change large image size
		$settings["media_library_sizes"]["lg"] = array(
			"name" => "image-2000",
			"width" => 2000
		);

		// Remove image "Title" presentation
		unset($settings["image_presentations"]["2"]);
		return $settings;
	}

	public function set_imageslider_settings ($settings) {
		$settings["use_media_library"] = True;
		$settings["media_library_sizes"] = array(
			"lg" => "image-2000", 	// 2000
			"md" => "image-1200",   // 1200);
			"sm" => "image-770"		// 770);
		);
		$settings["use_seperate_upload_folders"] = False;
		$settings["extra_class"] = "simple";

		return $settings;
	}

	public function render_destination_box ($atts) {
		require_once(THEME_DIR . '/template-parts/destination-box.php');
	    $a = shortcode_atts(array('id' => 0), $atts);
		ob_start();
		render_single_destination_box($a['id']);
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}



	/**
	 * Set KMC settigns
	 * @since Lapoint 1.0
	 */
	public function set_kmc_settings ($settings) {
		return array_merge($settings, array(
			"space" => array(
				"components" => array(
					"default_margin_top" => 0,
					"default_margin_bottom" => 0,
					"default_padding_top" => 76,
					"default_padding_bottom" => 50,
					"xs" => 10,
					"sm" => 30,
					"md" => 50,
					"lg" => 80,
					"xl" => 120
				)
			)
		));
	}

	/*
	public function add_permastruct( $rules ) {
		global $wp_rewrite;

		// set your desired permalink structure here
		$struct = '/%blog%/%postname%/';

		// use the WP rewrite rule generating function
		$rules = $wp_rewrite->generate_rewrite_rules(
			$struct,       // the permalink structure
			EP_PERMALINK,  // Endpoint mask: adds rewrite rules for single post endpoints like comments pages etc...
			true,         // Paged: add rewrite rules for paging eg. for archives (not needed here)
			true,          // Feed: add rewrite rules for feed endpoints
			false,          // For comments: whether the feed rules should be for post comments - on a singular page adds endpoints for comments feed
			false,         // Walk directories: whether to generate rules for each segment of the permastruct delimited by '/'. Always set to false otherwise custom rewrite rules will be too greedy, they appear at the top of the rules
			true           // Add custom endpoints
		);

		return $rules;
	}


	function custom_post_permalink( $permalink, $post) {

		// only do our stuff if we're using pretty permalinks
		// and if it's our target post type
		if ($post->post_type == 'post' && get_option('permalink_structure')) {
			// remember our desired permalink structure here
			// we need to generate the equivalent with real data
			// to match the rewrite rules set up from before

			$struct = '/%blog%/%postname%/';

			$log = false;

			if ($log) echo "!!!!!" . $permalink;

			$rewritecodes = array(
				'%blog%',
				'%postname%'
			);

			$blog = 'blog';
			if (strpos($permalink, "/da/")) {
				$blog = 'blog';
				$language_code = "/da";
			} else if (strpos($permalink, "/nb/")) {
				$language_code = "/nb";
				$blog = 'blogg';
			} else if (strpos($permalink, "/sv/")) {
				$language_code = "/sv";
				$blog = 'blogg';
			}

			$replacements = array(
				$blog,
				($leavename) ? '%postname%' : $post->post_name
			);

			// finish off the permalink
			$permalink = site_url() . $language_code . str_replace($rewritecodes, $replacements, $struct);
			//$permalink = home_url( $language_code . str_replace( $rewritecodes, $replacements, $struct ) );
			$permalink = user_trailingslashit($permalink, 'single');

			if ($log) echo " --> " . $permalink. "!!!!!!<br>";
		}

		return $permalink;
	}
	*/


	public function add_rewrite_rules() {

		//add_rewrite_tag('%blog%', '(blog|blogg)');
		/*add_rewrite_rule(
			'^/?$',
			'index.php?destination-type=surfcamp',
			'top'
		);*/

		add_rewrite_rule(
			'surfcamp/levels/?$',
			'index.php?pagename=surfcamps-levels',
			'top'
		);
		add_rewrite_rule(
			'kitecamp/levels/?$',
			'index.php?pagename=kitecamps-levels',
			'top'
		);

		add_rewrite_rule(
			'surfcamp/destinations/?$',
			'index.php?pagename=surfcamps-destinations',
			'top'
		);
		add_rewrite_rule(
			'kitecamp/destinations/?$',
			'index.php?pagename=kitecamps-destinations',
			'top'
		);
		add_rewrite_rule(
			'youthcamps/destinations/?$',
			'index.php?pagename=youthcamps-destinations',
			'top'
		);
		add_rewrite_rule(
			'ungdomslager/destinations/?$',
			'index.php?pagename=ungdomslager-destinations',
			'top'
		);
		add_rewrite_rule(
			'ungdomscamp/destinations/?$',
			'index.php?pagename=ungdomscamp-destinations',
			'top'
		);

		add_rewrite_rule(
			'yogasurfcamp/levels/?$',
			'index.php?pagename=yogasurfcamps-levels',
			'top'
		);

		add_rewrite_rule(
			'yogasurfcamp/destinations/?$',
			'index.php?pagename=yogasurfcamps-destinations',
			'top'
		);

		/*add_rewrite_rule(
			'(blogg|blog)/([^/]+)/?$',
			'index.php?pagename=$matches[2]',
			'top'
		);*/

		$desttype = '(surfcamp|kitecamp|youthcamps|ungdomslager|ungdomscamp|yogasurfcamp)';
		$loccamps = '(accommodations|indkvartering|overnatting|boenden)';

		add_rewrite_tag('%desttype%', $desttype);
		add_rewrite_tag('%loccamps%', $loccamps);


		// surfcamp/
		add_rewrite_rule(
			$desttype . '/?$',
			'index.php?destination-type=$matches[1]',
			'top'
		);

		// surfcamp/levels
		add_rewrite_rule(
			$desttype . '/levels/([^/]*)/?$',
			'index.php?level=$matches[2]&desttype=$matches[1]',
			'top'
		);

		// surfcamp/bali
		add_rewrite_rule(
			$desttype . '/([^/]*)/?$',
			'index.php?destination=$matches[2]&desttype=$matches[1]',
			'top'
		);

		// surfcamp/portugal/ericeira
		add_rewrite_rule(
			$desttype . '/([^/]*)/([^/]*)/?$',
			'index.php?location=$matches[3]&desttype=$matches[1]&dest=$matches[2]',
			'top'
		);

		// surfcamp/portugal/ericeira/boende/camp-lizandro
		add_rewrite_rule(
			$desttype . '/([^/]*)/([^/]*)/' . $loccamps . '/([^/]*)/?$',
			'index.php?camp=$matches[5]&desttype=$matches[1]&dest=$matches[2]&destloc=$matches[3]&loccamps=$matches[4]',
			'top'
		);

		// surfcamp/bali/boende/camp-bali
		add_rewrite_rule(
			$desttype . '/([^/]*)/' . $loccamps . '/([^/]*)/?$',
			'index.php?camp=$matches[4]&desttype=$matches[1]&dest=$matches[2]&loccamps=$matches[3]',
			'top'
		);


		// surfcamp/portugal/ericeira/levels/level-1
		add_rewrite_rule(
			$desttype . '/([^/]*)/([^/]*)/levels/([^/]*)/?$',
			'index.php?package=$matches[4]&desttype=$matches[1]&dest=$matches[2]&destloc=$matches[3]',
			'top'
		);

		// surfcamp/bali/levels/level-1
		add_rewrite_rule(
			$desttype . '/([^/]*)/levels/([^/]*)/?$',
			'index.php?package=$matches[3]&desttype=$matches[1]&dest=$matches[2]',
			'top'
		);

		/*
		add_rewrite_rule(
			'test/kitecamp/([^/]*)/?$',
			//'index.php?pagename=$matches[1]&eventdate=$matches[2]',
			'index.php?destination=$matches[1]&desttype=kitecamp',
			'top'
		);
		*/
	}

	/*function sniff_requests ($wp_query) {
		echo "************";
		global $wp;
		var_dump($wp_query);
		echo $wp_query->query_vars['desttype'];
		echo $wp->query_vars['desttype'];
		echo $_GET['desttype'];
		echo "************";
	}*/



	public function acf_destination_field ($title, $post, $field) {
		return $title ." - " . get_field("destination_type", $post->ID)->post_title;
	}

	public function change_the_title ($title) {
		global $post;
		if ($post->post_name == "surfcamp") {
			return get_post_meta($post->ID, '_amt_title', true);
		}
		return $title;
	}



	# ****************************** Admin Init ******************************
	public function admin_menu () {
		 add_menu_page(
	        'Booking overview',
	        'Booking',
	        'manage_options',
	        'custompage',
	        array($this, 'booking_overview_page'),
	        'dashicons-cart',
	        59
    	);
	}
	public function booking_overview_page () {
		wp_enqueue_script('booking-overview', THEME_URL . '/admin/js/booking-overview.js', array(), 2);
		include("admin/booking-overview.php");
	}


	# ****************************** Init ******************************
	public function init () {

		if (isset($_GET["page"]) && $_GET["page"] == "fix-images") {

			include "scripts/fix-images.php";

		} else if (isset($_GET["page"]) && $_GET["page"] == "set-meta") {

			include "scripts/set-meta.php";


		} else if (isset($_GET["page"]) && $_GET["page"] == "set-canonical") {

			include "scripts/set-canonical.php";


		} else if (isset($_GET["page"]) && $_GET["page"] == "list-urls") {
			$host = home_url();


			global $destination_types_manager;
			$destinations_types = get_posts(array(
				'posts_per_page' => -1,
				'post_type' => 'destination-type',
				'orderby' => 'title',
				'order' => 'ASC',
				'post_status' => 'publish',
				'suppress_filters' => 1
			));

			echo "Destination types:<br>";
			$count = 0;
			foreach ($destinations_types as $destinations_type) {

				$dest = new Destination_Type($destinations_type);
				$post_language_details = apply_filters( 'wpml_post_language_details', NULL, $dest->id ) ;
				$code = $post_language_details["language_code"];
				
				$link = $dest->link;
				if( !$link ) {
					$link = $destination_types_manager->custom_post_permalink("", $destinations_type, false, false);
					if ($code != "en") {
						$link = str_replace($host, $host . "/" . $code, $link);
					}
				}

				echo $dest->title ." (". $code ."): <a href='". $link ."' target='_blank'>". $link ."</a><br>";
				$count++;
			}
			echo "Count: " . $count ."<br>";

			echo "<br><hr><br>";


			global $destinations_manager;
			$destinations = get_posts(array(
				'posts_per_page' => -1,
				'post_type' => 'destination',
				'orderby' => 'title',
				'order' => 'ASC',
				'post_status' => 'publish',
				'suppress_filters' => 1
			));

			echo "Destinations:<br>";
			$count = 0;
			foreach ($destinations as $destination) {
				$dest = new Destination($destination);
				$post_language_details = apply_filters( 'wpml_post_language_details', NULL, $dest->id ) ;
				$code = $post_language_details["language_code"];
				$link = $dest->link;
				if( !$link ) {
					$link = $destinations_manager->custom_post_permalink("", $destination, false, false);
					if ($code != "en") {
						$link = str_replace($host, $host . "/" . $code, $link);
					}
				}
				echo $dest->title ." (". $code ."): <a href='". $link ."' target='_blank'>". $link ."</a><br>";
				$count++;
			}
			echo "Count: " . $count ."<br>";

			echo "<br><hr><br>";


			global $levels_manager;
			$levels = get_posts(array(
				'posts_per_page' => -1,
				'post_type' => 'level',
				'orderby' => 'title',
				'order' => 'ASC',
				'post_status' => 'publish',
				'suppress_filters' => 1
			));

			echo "Levels:<br>";
			$count = 0;
			foreach ($levels as $level) {
				$ll = new Level($level);
				$post_language_details = apply_filters( 'wpml_post_language_details', NULL, $ll->id ) ;
				$code = $post_language_details["language_code"];
				$link = $dest->link;
				if( !$link ) {
					$link = $levels_manager->custom_post_permalink("", $level, false, false);
					if ($code != "en") {
						$link = str_replace($host, $host . "/" . $code, $link);
					}
				}
				echo $ll->title ." (". $code ."): <a href='". $link ."' target='_blank'>". $link ."</a><br>";
				$count++;
			}
			echo "Count: " . $count ."<br>";

			echo "<br><hr><br>";



			global $packages_manager;
			$packages = get_posts(array(
				'posts_per_page' => -1,
				'post_type' => 'package',
				'orderby' => 'title',
				'order' => 'ASC',
				'post_status' => 'publish',
				'suppress_filters' => 1
			));

			echo "Packages:<br>";
			$count = 0;
			foreach ($packages as $package) {
				$pp = new Package($package);
				$post_language_details = apply_filters( 'wpml_post_language_details', NULL, $pp->id ) ;
				$code = $post_language_details["language_code"];
				$link = $dest->link;
				if( !$link ) {
					$link = $packages_manager->custom_post_permalink("", $package, false, false);
					if ($code != "en") {
						$link = str_replace($host, $host . "/" . $code, $link);
					}
				}
				echo $pp->title ." (". $code ."): <a href='". $link ."' target='_blank'>". $link ."</a><br>";
				$count++;
			}
			echo "Count: " . $count ."<br>";

			echo "<br><hr><br>";


			global $camps_manager;
			$camps = get_posts(array(
				'posts_per_page' => -1,
				'post_type' => 'camp',
				'orderby' => 'title',
				'order' => 'ASC',
				'post_status' => 'publish',
				'suppress_filters' => 1
			));

			echo "Camps:<br>";
			$count = 0;
			foreach ($camps as $camp) {
				$cc = new Camp($camp);
				$post_language_details = apply_filters( 'wpml_post_language_details', NULL, $cc->id ) ;
				$code = $post_language_details["language_code"];
				$link = $dest->link;
				if( !$link ) {
					$link = $camps_manager->custom_post_permalink("", $camp, false, false);
					//if ($code != "en") {
					//	$link = str_replace($host, $host . "/" . $code, $link);
					//}
				}
				echo $cc->title ." (". $code ."): <a href='". $link ."' target='_blank'>". $link ."</a><br>";
				$count++;
			}
			echo "Count: " . $count ."<br>";


			echo "<br><hr><br>";

			/*
			$pages = get_posts(array(
				'posts_per_page' => -1,
				'post_type' => 'page',
				'orderby' => 'title',
				'order' => 'ASC',
				'post_status' => 'publish',
				'suppress_filters' => 1
			));

			echo "Page:<br>";
			$count = 0;
			foreach ($pages as $page) {
				$page_language_details = apply_filters('wpml_post_language_details', NULL, $page->ID);
				$code = $page_language_details["language_code"];
				$link = home_url(get_page_uri($page->ID));
				if ($code != "en") {
					$link = str_replace($host, $host . "/" . $code, $link);
				}
				echo $page->post_title ." (". $code ."): <a href='". $link ."' target='_blank'>". $link ."</a><br>";
				$count++;
			}
			echo "Count: " . $count ."<br>";

			echo "<br><hr><br>";

			$posts = get_posts(array(
				'posts_per_page' => -1,
				'post_type' => 'post',
				'orderby' => 'title',
				'order' => 'ASC',
				'post_status' => 'publish',
				'suppress_filters' => 1
			));

			echo "Posts:<br>";
			$count = 0;
			foreach ($posts as $post) {
				$post_language_details = apply_filters( 'wpml_post_language_details', NULL, $post->ID ) ;
				$code = $post_language_details["language_code"];
				$link = home_url(get_page_uri($post->ID));
				if ($code != "en") {
					$link = str_replace($host, $host . "/" . $code, $link);
				}
				//echo $post->ID ." : ";
				echo $post->post_title ." (". $code ."): <a href='". $link ."' target='_blank'>". $link ."</a><br>";
				//echo $post->post_title ." (". $code ."): ". get_permalink($post->ID);
				//if ($code != "sv") echo "?lang=" . $code;
				//echo "<br>";
				$count++;
			}
			echo "Count: " . $count ."<br>";
			*/
			exit();
		}
	}


	/**
	 * Register which post types that should be administered with KMC
	 * @since Lapoint2016 1.0
	 */
	public function set_kmc_admin_types($types) {
		array_push($types, "destination-type", "destination", "level", "camp", "package", "post", "location");
		return $types;
	}


	/**
	 * Determine if the page composer should be used when editing a single page
	 * @since Lapoint2016 1.0
	 */
	public function set_kmc_edit($bool) {
		$post_id = $_GET['post'] ? $_GET['post'] : $_POST['post_ID'];
		$template_file = get_post_meta($post_id, '_wp_page_template', true);
		if ($template_file == 'page-kmc.php') {
			return true;
		}
		return $bool;
	}


	/**
	 * Register KMC categories used in the admin section
	 * @since Lapoint2016 1.0
	 */
	public function set_kmc_categories($categories) {
		$categories["lapoint"] = (object) array(
			"title" => "Lapoint specific",
			//"icon" => plugins_url('img/diamond.svg', __FILE__)
			"icon" => THEME_URL . '/img/kmc/diamond.svg'
		);
		return $categories;
	}


	# ****************************** Setup ACF ******************************
	public function register_acf_fields () {
		if (function_exists("register_field_group")) {
			register_field_group(array (
				'id' => 'acf_page-composer-settings',
				'title' => 'Page composer settings',
				'fields' => array (
					array (
						'key' => 'field_56f120d62a370',
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
				),
				'location' => array (
					array (
						array (
							'param' => 'page_template',
							'operator' => '==',
							'value' => 'page-kmc.php',
							'order_no' => 0,
							'group_no' => 0,
						),
					),
				),
				'options' => array (
					'position' => 'side',
					'layout' => 'default',
					'hide_on_screen' => array (
					),
				),
				'menu_order' => 0,
			));

			register_field_group(array (
				'id' => 'acf_post-extra-settings',
				'title' => 'Post settings',
				'fields' => array (
					array (
						'key' => 'field_5731a8477fec8',
						'label' => 'Rel canonical',
						'name' => 'rel_canonical',
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
							'value' => 'post',
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




	/**
	 * Change main query on home to show a destination type
	 * @since Lapoint2016 1.0
	 */
	public function modify_main_query( $query ) {
		
		if ($query->is_home() && $query->is_main_query()) {
			$query->set('post_type' ,'destination-type');
			$query->set( 'posts_per_page', 1 );
			$query->set( 'is_single', true );
			$query->set( 'is_posts_page', false );
			$query->set( 'is_home', false );
			$query->set( 'meta_key', front_page );
			$query->set( 'meta_value', 1 );
		}
	}

	/**
	 * Change template on home to show a destination type
	 * @since Lapoint2016 1.0
	 */
	public function modify_home_template ($template) {
		if ( is_home() ) {
			$new_template = locate_template( array( 'single-destination-type.php' ) );
			if ($new_template) {
				return $new_template ;
			}
		}
		return $template;
	}
	public function amt_get_queried_object ($amt_post) {
		if (is_home() && !$amt_post) {
			global $post;
			$amt_post = $post;
			return $post;
		}
		return $amt_post;
	}


	public function register_kcm_modules () {
		include_once('kmc-modules/kmc-levels/kmc-levels-class.php');
		include_once('kmc-modules/kmc-destinations/kmc-destinations-class.php');
		include_once('kmc-modules/kmc-booking-bar/kmc-booking-bar-class.php');
		include_once('kmc-modules/kmc-header/kmc-header-class.php');
		include_once('kmc-modules/kmc-packages/kmc-packages-class.php');
		include_once('kmc-modules/kmc-camps/kmc-camps-class.php');
		include_once('kmc-modules/kmc-locations/kmc-locations-class.php');
		include_once('kmc-modules/kmc-videos/kmc-videos-class.php');
	}

	public function set_wp_revision_count( $num, $post ) {
		return 2;
	}


	# ****************************** After Setup ******************************
	public function after_setup () {
		add_theme_support('post-thumbnails');

		add_theme_support( 'title-tag' );

		add_image_size('header-image', 2500);
		add_image_size('image-2000', 2000);
		add_image_size('image-1200', 1200);
		add_image_size('image-770', 770);
		//add_image_size('header-image-fixed', 2500, 275, true);

		// add_image_size('large-thumb', 750, 320, true);
		// add_image_size('medium-thumb', 600, 1600, false);
		// add_image_size('small-thumb', 300, 800, false);

		add_image_size('box-md', 777, 360, true);
		add_image_size('box-sm', 415, 360, true);

		add_image_size('box-tall-md', 777, 730, true);
		add_image_size('box-tall-sm', 415, 730, true);

		add_image_size('rect-md', 600, 400, true);
		add_image_size('rect-sm', 330, 220, true);
		//add_image_size('box-md', 750, 360, true);

		// This theme uses wp_nav_menu() in two location.
		register_nav_menus(array(
			'primary' => __('Primary Navigation', 'lapoint'),
			'secondary' => __('Secondary Navigation', 'lapoint')
		));
	}
	public function widgets_init () {
		register_sidebar(array(
			'name' => __('Sidebar', "lapoint"),
			'id' => 'posts-sidebar',
			'description' => __('Appears at the side of post pages', 'lapoint'),
			'before_title' => '<h3>',
			'after_title' => '</h3>',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' =>'</div>')
		);
		register_sidebar(array(
			'name' => __('Footer'),
			'id' => 'footer',
			'description' => __('Appears at the bottom of the page', 'lapoint'),
			'before_title' => '<h3>',
			'after_title' => '</h3>',
			'before_widget' => '<div id="%1$s" class="widget %2$s col-sm-6 col-md-6">',
			'after_widget' =>'</div>')
		);
		register_sidebar(array(
			'name' => __('Footer right col'),
			'id' => 'footer-right-col',
			'description' => __('The right column of the footer is a seperate widget area', 'lapoint'),
			'before_title' => '<h3>',
			'after_title' => '</h3>',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' =>'</div>')
		);



		register_widget('Lapoint_Social_Widget');
		register_widget('Lapoint_Newsletter_Widget');
	}


	# ****************************** Excerpt ******************************
	public function new_excerpt_more( $more ) {
	  return '...<br> <a class="read-more btn btn-secondary" href="' . get_permalink(get_the_ID()) . '">'. __("Read more", "lapoint") . '</a>';
	}
	public function custom_excerpt_length( $length ) {
		return 40;
	}



	# ******************************  Setup Theme Data ******************************
	public function setup_theme_data () {

		if (is_admin()) return;

		global $CAMP, $DESTINATION_TYPE, $DESTINATION, $LEVEL, $PACKAGE, $LOCATION;

		$DESTINATION_TYPE = false;
		$DESTINATION = false;
		$LEVEL = false;
		$PACKAGE = false;
		$CAMP = false;
		$LOCATION = false;

		global $post;
		
		switch (get_post_type()) {
			case 'destination-type':
				$this->assure_correct_url();
				$DESTINATION_TYPE = new Destination_Type($post);
				break;
			case 'destination':
				$this->assure_correct_url();
				$DESTINATION = new Destination($post);
				$DESTINATION_TYPE = $DESTINATION->get_type();
				break;
			case 'package':
				$this->assure_correct_url();
				$PACKAGE = new Package($post);
				$DESTINATION = $PACKAGE->get_destination();
				$DESTINATION_TYPE = $DESTINATION->get_type();
				break;
			case 'level':
				$this->assure_correct_url();
				$LEVEL = new Level($post);
				$DESTINATION_TYPE = $LEVEL->get_type();
				break;
			case 'page':
				$destination_type = get_field("destination_type");
				if ($destination_type) {
					$DESTINATION_TYPE = new Destination_Type($destination_type);
				}
				break;
			case 'camp':
				$this->assure_correct_url();
				$CAMP = new Camp($post);
				$DESTINATION = $CAMP->get_destination();
				$DESTINATION_TYPE = $DESTINATION->get_type();
				break;
			case 'location':
				$LOCATION = new Location($post);
				$DESTINATION = $LOCATION->get_destination();
				$DESTINATION_TYPE = $DESTINATION->get_type();
				break;
			case '':
				header("HTTP/1.0 404 Not Found");
			    global $wp_query;
			    $wp_query->set_404();
			    require THEME_DIR . '/404.php';
			    exit;
				break;
		}
	}

	private function assure_correct_url () {
		$url = strtok($_SERVER[REQUEST_URI], '?');
		if (!is_admin() && !strpos(get_permalink($post), $url)) {
			header("Location: " . get_permalink($post));
			exit();
		}
	}


	# ******************************  Enqueue scripts and styles ******************************
	public function lapoint_scripts () {
		
		wp_enqueue_script('current-device', THEME_URL . '/js/vendor/current-device.min.js');

		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('jquery-detect-swipe', THEME_URL . '/js/vendor/jquery.detect_swipe-custom.js');
		wp_enqueue_script('underscore');
		wp_enqueue_script('backbone');


		wp_localize_script('jquery', 'ajaxlapoint', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'postNonce' => wp_create_nonce('lapoint-post-nonce')
		));

		//wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

		wp_enqueue_style('select2-style', THEME_URL . '/css/select2.min.css');

		wp_enqueue_script('booking-bar-script', THEME_URL . '/kmc-modules/kmc-booking-bar/js/booking-bar.js', array(), 3);
		wp_enqueue_script('select-2', THEME_URL . '/js/vendor/select2.min.js');
		wp_enqueue_script('iframeResizer', THEME_URL . '/js/vendor/iframeResizer.min.js');


		// Load our main stylesheet.
		wp_enqueue_style('lapoint-style', get_stylesheet_uri(), array(), $this->version);
	}
	public function admin_enqueue_scripts () {
		wp_enqueue_style('lapoint-admin', THEME_URL . '/admin.css', array(), $this->version);
		add_editor_style('admin-editor-style.css');
	}


	# ******************************  Custom admin list columns ******************************
	private function get_admin_row_object ($cls, $post_id) {
		global $admin_row_objects;
		if (!isset($admin_row_objects)) {
			$admin_row_objects = array();
		}

		if (!isset($admin_row_objects[$post_id])) {
			$admin_row_objects[$post_id] = new $cls($post_id);
		}

		return $admin_row_objects[$post_id];
	}
	public function custom_columns ($column, $post_id) {
		$post_type = get_post_type($post_id);

		switch ($column){
			case "type":
				if ($post_type == "destination") {
					$destination = $this->get_admin_row_object(Destination, $post_id);
					$destination_type = $destination->get_type();
					echo $destination_type ? $destination_type->title : "-";
				}
				break;
			case "destination":
				if ($post_type == "location") {
					$location = $this->get_admin_row_object(Location, $post_id);
					$destination = $location->get_destination();
					echo $destination ? $destination->title : "-";
				} else if ($post_type == "camp") {
					$camp = $this->get_admin_row_object(Camp, $post_id);
					$destination = $camp->get_destination();
					echo $destination ? $destination->title : "-";
				} else if ($post_type == "package") {
					$package = $this->get_admin_row_object(Package, $post_id);
					$destination = $package->get_destination();
					echo $destination ? $destination->title : "-";
				}
				break;
			case "location":
				if ($post_type == "camp") {
					$camp = $this->get_admin_row_object(Camp, $post_id);
					$location = $camp->get_location();
					echo $location ? $location->title : "-";
				} else if ($post_type == "package") {
					$package = $this->get_admin_row_object(Package, $post_id);
					$location = $package->get_location();
					echo $location ? $location->title : "-";
				}
				break;
			case "level":
				if ($post_type == "package") {
					$package = $this->get_admin_row_object(Package, $post_id);
					$location = $package->get_level();
					echo $location ? $location->title : "-";
				}
				break;
		}
	}

	# ****************************** Pimp TinyMCE ******************************
	/**
	 * Add additional buttons to the tinyMCE editor
	 * @since Lapoint2016 1.0
	 */
	function add_tinymce_buttons() {
		if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) return;
		if (get_user_option('rich_editing') == 'true'){
			add_filter('mce_external_plugins', array($this, 'add_tiny_mce_plugin'));
			add_filter('mce_buttons', array($this, 'tiny_mce_buttons_1'));
			add_filter('mce_buttons_2', array($this, 'tiny_mce_buttons_2'));
			// add_filter('tiny_mce_before_init', array($this, 'set_tiny_mce_formats'));
		}
	}

	function tiny_mce_buttons_1($buttons){
		array_push($buttons, "|", "ingress", "twocol", "twocolleft", "twocolright", "threecol", "outsidecontent");
	   return $buttons;
	}
	function add_tiny_mce_plugin($plugin_array) {
	   $plugin_array['editorcolumns'] = THEME_URL .'/admin/js/editor-columns.js';
	   return $plugin_array;
	}
	function tiny_mce_buttons_2( $buttons ) {
		array_unshift( $buttons, 'styleselect', 'hr', 'sup', 'sub');
		return $buttons;
	}


	/******************************* Misc ******************************
	* Fix wordpress admin bar flicker in chrome issue
	*/
	public function admin_menu_fix() {
		echo '<style>
		#adminmenu { transform: translateZ(0); }
		</style>';
	}


	# ****************************** Ajax ******************************
	private function json_response ($output) {
		header('Content-Type: application/json');
		echo json_encode($output);
		die();
	}

	public function ajax_update_booking_code () {
		if ($_POST["booking_code"] && $_POST["post_id"]) {
			$post_id = $_POST["post_id"];
			$post = get_post($post_id);
			$post_type = $post->post_type;
			$booking_code = $_POST["booking_code"];

			// The post id is for EN, fetch the other:
			$sv_post_id = icl_object_id($post_id, $post_type, false, "sv");
			$da_post_id = icl_object_id($post_id, $post_type, false, "da");
			$nb_post_id = icl_object_id($post_id, $post_type, false, "nb");

			update_post_meta($post_id, 'booking_code', $booking_code);
			update_post_meta($sv_post_id, 'booking_code', $booking_code);
			update_post_meta($da_post_id, 'booking_code', $booking_code);
			update_post_meta($nb_post_id, 'booking_code', $booking_code);

			if (isset($_POST["has_booking_label"]) && $_POST["has_booking_label"] == "1") {
				update_post_meta($post_id, 'booking_label', $_POST["en_booking_label"]);
				update_post_meta($post_id, '_booking_label', 'field_572b9de2c0b0f');
				update_post_meta($sv_post_id, 'booking_label', $_POST["sv_booking_label"]);
				update_post_meta($sv_post_id, '_booking_label', 'field_572b9de2c0b0f');
				update_post_meta($da_post_id, 'booking_label', $_POST["da_booking_label"]);
				update_post_meta($da_post_id, '_booking_label', 'field_572b9de2c0b0f');
				update_post_meta($nb_post_id, 'booking_label', $_POST["nb_booking_label"]);
				update_post_meta($nb_post_id, '_booking_label', 'field_572b9de2c0b0f');
			}

			$this->json_response(array(
				'status' => 200
			));
		} else {
			$this->json_response(array(
				'status' => 500
			));
		}
	}

	public function ajax_fetch_packages () {
		if (isset($_GET["destination"]) && isset($_GET["level"])) {
			$destination_id = $_GET["destination"];
			$level_id = $_GET["level"];

			global $destinations_manager;
			$destination = $destinations_manager->get($destination_id);

			$this->json_response(array(
				'status' => 200,
				'package' => $destination->get_package_for_level($level_id)
			));
		} else {
			$this->json_response(array(
				'status' => 200
			));
		}
	}

}




# ****************************** Lapoint Newsletter Widget ******************************
class Lapoint_Newsletter_Widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'Lapoint_Newsletter_Widget',
			__('Lapoint newsletter registration', 'lapoint'),
			array(
				'description' => __('Registration form for newsletter', 'lapoint')
			)
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		include("widgets/newsletter.php");
		echo $args['after_widget'];
	}
}


# ****************************** Lapoint Social Widget ******************************
class Lapoint_Social_Widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'Lapoint_Social_Widget',
			__('Lapoint social links', 'lapoint'),
			array(
				'description' => __('Social icons for Lapoint', 'lapoint')
			)
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		include("widgets/social.php");
		echo $args['after_widget'];
	}
}


function wpml_get_code( $lang = "" ) {
	$langs = icl_get_languages( 'skip_missing=0' );
	if ( isset( $langs[$lang]['default_locale'] ) ) {
		return $langs[$lang]['default_locale'];
	}

	return false;
}


$lapoint_theme = new Lapoint_Framework();
