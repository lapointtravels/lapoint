<?php
/**
 * @package Collecta
 * @version 1.0
 */
/*
Plugin Name: Collecta
Description: User information registration
Author: Christian Wannerstedt @ Kloon Production AB
Version: 1.0
Author URI: http://www.kloon.se
*/

require_once(dirname(__FILE__) .'/lib/utils.php');

class WPCollecta {

    protected $pluginPath;
    protected $pluginUrl;
    protected $tableName;

	public function __construct(){
        // Set Plugin Path
        $this->pluginPath = dirname(__FILE__);

        // Set Plugin URL
        $this->pluginUrl = WP_PLUGIN_URL . '/collecta';

		global $wpdb;
        $this->tableName = $wpdb->prefix . 'collecta_users';

		register_activation_hook( __FILE__, array($this, 'install'));
		register_deactivation_hook( __FILE__, array($this, 'uninstall'));

		add_action('wp_ajax_nopriv_submit-form', array($this, 'collecta_submit_form'));
		add_action('wp_ajax_submit-form', array($this, 'collecta_submit_form'));

		add_action('admin_menu', array($this, 'admin_menu'));
	    //add_action('admin_init', array($this, 'page_init'));

	    add_action('wp_ajax_collecta-fetch-users', array($this, 'collecta_fetch_users'));
	}


	// ****************************** Install / Uninstall ******************************
	public function install(){
		global $wpdb;
		$structure = "CREATE TABLE ". $this->tableName ." (
		  `id` int(9) unsigned NOT NULL auto_increment,
		  `name` varchar(255) character set utf8 collate utf8_general_ci NOT NULL,
		  `email` varchar(255) character set utf8 collate utf8_general_ci NOT NULL,
		  `lang` varchar(2) character set utf8 collate utf8_general_ci NOT NULL,
		  `ip` varchar(15) character set utf8 collate utf8_general_ci NOT NULL,
		  `created` datetime NOT NULL,
		  `updated` datetime NOT NULL,
		  PRIMARY KEY  (`id`)
			) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";
		$wpdb->query($structure);
	}
	public function uninstall(){
		global $wpdb;
		$wpdb->query("DROP TABLE IF EXISTS `". $this->tableName ."`;");
	}


	// ****************************** Administration ******************************
	public function admin_menu() {
		add_menu_page(__('Collected info', 'collecta'), __('Collected info', 'collecta'), 'manage_options', 'collecta-user-list', array($this, 'collecta_list_view'));
		add_submenu_page('collecta-user-list', 'Collected users', 'Collected users', 'manage_options', 'collecta-user-list', array($this, 'collecta_list_view'));
		add_submenu_page('collecta-user-list', 'General settings', 'General settings', 'manage_options', 'collecta-settings', array($this, 'collecta_settings_view'));
	}


	// ****************************** Settings view ******************************
	public function collecta_settings_view(){
		collecta_assert_admin_access();

		// Update settings
		if (collecta_is_action("update-settings")){
			if (function_exists(icl_get_languages)){
				$languages = icl_get_languages('skip_missing=0&orderby=code');
				if (!empty($languages)){
					foreach($languages as $l){
						$code = '-'. $l['language_code'];
						$this->updateSettings($code);
				    }
				}
			} else {
				$this->updateSettings('');
			}
		}

		wp_enqueue_style('collecta_admin_style', plugins_url('css/admin.css', __FILE__));
		require_once(dirname(__FILE__) .'/admin-settings.php');

	}
	private function updateSettings($code){
		if (isset($_POST["collecta-name-placeholder". $code])){
			update_option("collecta-name-placeholder". $code, $_POST["collecta-name-placeholder". $code]);
		}
		if (isset($_POST["collecta-email-placeholder". $code])){
			update_option("collecta-email-placeholder". $code, $_POST["collecta-email-placeholder". $code]);
		}
		if (isset($_POST["collecta-thanks". $code])){
			update_option("collecta-thanks". $code, $_POST["collecta-thanks". $code]);
		}
		if (isset($_POST["collecta-email-to". $code])){
			update_option("collecta-email-to". $code, $_POST["collecta-email-to". $code]);
		}
		if (isset($_POST["collecta-email-sender". $code])){
			update_option("collecta-email-sender". $code, $_POST["collecta-email-sender". $code]);
		}
		if (isset($_POST["collecta-email-sender-mail". $code])){
			update_option("collecta-email-sender-mail". $code, $_POST["collecta-email-sender-mail". $code]);
		}
		if (isset($_POST["collecta-email-subject". $code])){
			update_option("collecta-email-subject". $code, $_POST["collecta-email-subject". $code]);
		}
		if (isset($_POST["collecta-email-body". $code])){
			update_option("collecta-email-body". $code, $_POST["collecta-email-body". $code]);
		}

	}


	// ****************************** List view ******************************
	public function collecta_list_view(){
		collecta_assert_admin_access();
		wp_enqueue_script('collecta_admin_list', plugins_url('js/admin-list.js', __FILE__));
		wp_enqueue_style('collecta_admin_style', plugins_url('css/admin.css', __FILE__));

		global $wpdb, $totalCount, $users;
		$res = $wpdb->get_row(sprintf("SELECT COUNT(*) AS totalCount FROM %s;", $this->tableName));
		$totalCount = $res->totalCount;

		$users = $wpdb->get_results(sprintf("SELECT * FROM %s ORDER BY name LIMIT 50;", $this->tableName));

		require_once(dirname(__FILE__) .'/admin-list.php');
	}
	public function collecta_fetch_users(){
		collecta_assert_admin_access();
		global $wpdb, $users;

		$sort = (isset($_GET['sort'])) ? esc_sql($_GET['sort']) : 'name';
		$order = (isset($_GET['order'])) ? esc_sql($_GET['order']) : 'asc';
		$per_page = (isset($_GET['per_page']) && is_numeric($_GET['per_page'])) ? $_GET['per_page'] : 50;
		$page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;

		$users = $wpdb->get_results(sprintf("SELECT * FROM %s ORDER BY %s %s LIMIT %d, %d;", $this->tableName, $sort, $order, ($page - 1) * $per_page, $per_page));

		header( "Content-Type: application/json" );
		echo json_encode(array(
			'status' => 200,
			'data' => array(
				'users' => $users
			)
		));
		exit;
	}


	// ****************************** Front end ******************************
	public function setupForm(){
		wp_enqueue_script('jquery');
		wp_enqueue_script('collect_main', plugins_url('js/collecta-main.js', __FILE__), array(), 2);
		wp_localize_script('collect_main', 'CollectaAjax', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'postNonce' => wp_create_nonce( 'collecta-post-nonce' )
		));
	}

	public function collecta_submit_form() {
		$nonce = $_POST['postNonce'];
 		if (!wp_verify_nonce($nonce, 'collecta-post-nonce')) die ('Invalid nonce!');

		//$name = esc_sql($_POST['name']);
		$email = esc_sql($_POST['email']);
		$lang = esc_sql($_POST['lang']);

		// Save in db
		global $wpdb;
		$wpdb->get_results(sprintf("INSERT INTO %s (email,lang,ip,created) VALUES ('%s', '%s', '%s', NOW());", $this->tableName, $email, $lang, $_SERVER["REMOTE_ADDR"]));

		// Send mail
		$code = ($lang) ? "-". $lang : "";
		$sendTo = get_option('collecta-email-to'. $code);
		if ($sendTo){
			$subject = get_option('collecta-email-subject'. $code);
			$headers = sprintf("From: \"%s\" <%s>\n", get_option('collecta-email-sender'. $code), get_option('collecta-email-sender-mail'. $code));
			$headers .= "Reply-To: ". get_option('collecta-email-sender-mail'. $code) ."\n";
			$headers .= "MIME-Version: 1.0\n";
			$headers .= "Content-type: text/html; charset=utf-8\n";
			$headers .= "X-Priority: 3\n";
			$headers .= "X-MSMail-Priority: Normal\n";
			$headers .= "X-Mailer: PHP/". phpversion() ."\n";
			$mailContent = get_option('collecta-email-body'. $code) ."
E-post: ". $email;
			mail($sendTo, $subject, $mailContent, $headers);
		}


		// Response output
		header( "Content-Type: application/json" );
		echo json_encode(array(
			'status' => 200
		));
		exit;
	}



}

global $wpCollecta;
$wpCollecta = new WPCollecta();
?>