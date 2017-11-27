<?php
/**
 * Plugin Name: ShortPixel Image Optimizer
 * Plugin URI: https://shortpixel.com/
 * Description: ShortPixel optimizes images automatically, while guarding the quality of your images. Check your <a href="options-general.php?page=wp-shortpixel" target="_blank">Settings &gt; ShortPixel</a> page on how to start optimizing your image library and make your website load faster. 
 * Version: 4.4.0
 * Author: ShortPixel
 * Author URI: https://shortpixel.com
 * Text Domain: shortpixel-image-optimiser
 * Domain Path: /lang
 */

define('SP_RESET_ON_ACTIVATE', false); //if true TODO set false
//define('SHORTPIXEL_DEBUG', true);

define('SHORTPIXEL_PLUGIN_FILE', __FILE__);

define('SP_AFFILIATE_CODE', '');

define('PLUGIN_VERSION', "4.4.0");
define('SP_MAX_TIMEOUT', 10);
define('SP_VALIDATE_MAX_TIMEOUT', 15);
define('SP_BACKUP', 'ShortpixelBackups');
define('MAX_API_RETRIES', 50);
define('MAX_ERR_RETRIES', 5);
define('MAX_FAIL_RETRIES', 3);
$MAX_EXECUTION_TIME = ini_get('max_execution_time');

require_once(ABSPATH . 'wp-admin/includes/file.php');

$sp__uploads = wp_upload_dir();
define('SP_UPLOADS_BASE', $sp__uploads['basedir']);
define('SP_UPLOADS_NAME', basename(is_main_site() ? SP_UPLOADS_BASE : dirname(dirname(SP_UPLOADS_BASE))));
define('SP_UPLOADS_BASE_REL', str_replace(get_home_path(),"", $sp__uploads['basedir']));
$sp__backupBase = is_main_site() ? SP_UPLOADS_BASE : dirname(dirname(SP_UPLOADS_BASE));
define('SP_BACKUP_FOLDER', $sp__backupBase . '/' . SP_BACKUP);

/*
 if ( is_numeric($MAX_EXECUTION_TIME)  && $MAX_EXECUTION_TIME > 10 )
    define('MAX_EXECUTION_TIME', $MAX_EXECUTION_TIME - 5 );   //in seconds
else
    define('MAX_EXECUTION_TIME', 25 );
*/

define('MAX_EXECUTION_TIME', 2 );
define("SP_MAX_RESULTS_QUERY", 6);    

function shortpixelInit() {
    global $pluginInstance;
    //is admin, is logged in - :) seems funny but it's not, ajax scripts are admin even if no admin is logged in.
    $prio = get_option('wp-short-pixel-priorityQueue');
    if (!isset($pluginInstance)
        && (($prio && is_array($prio) && count($prio) && get_option('wp-short-pixel-front-bootstrap'))
            || is_admin()
               && (function_exists("is_user_logged_in") && is_user_logged_in())
               && (   current_user_can( 'manage_options' )
                   || current_user_can( 'upload_files' )
                   || current_user_can( 'edit_posts' )
                  )
           )
       ) 
    {
        require_once('wp-shortpixel-req.php');
        $pluginInstance = new WPShortPixel;
    }
} 

function handleImageUploadHook($meta, $ID = null) {
    global $pluginInstance;
    if(!isset($pluginInstance)) {
        require_once('wp-shortpixel-req.php');
        $pluginInstance = new WPShortPixel;
    }
    return $pluginInstance->handleMediaLibraryImageUpload($meta, $ID);
}

function shortpixelNggAdd($image) {
    global $pluginInstance;
    if(!isset($pluginInstance)) {
        require_once('wp-shortpixel-req.php');
        $pluginInstance = new WPShortPixel;
    }
    $pluginInstance->handleNextGenImageUpload($image);
}

function shortPixelActivatePlugin () {
    require_once('wp-shortpixel-req.php');
    WPShortPixel::shortPixelActivatePlugin();    
}

function shortPixelDeactivatePlugin () {
    require_once('wp-shortpixel-req.php');
    WPShortPixel::shortPixelDeactivatePlugin();    
}

if ( !function_exists( 'vc_action' ) || vc_action() !== 'vc_inline' ) { //handle incompatibility with Visual Composer
    add_action( 'init',  'shortpixelInit');
    add_action('ngg_added_new_image', 'shortpixelNggAdd');
    
    $autoMediaLibrary = get_option('wp-short-pixel-auto-media-library');
    if($autoMediaLibrary) {
        add_filter( 'wp_generate_attachment_metadata', 'handleImageUploadHook', 10, 2 );
    }
    
    register_activation_hook( __FILE__, 'shortPixelActivatePlugin' );
    register_deactivation_hook( __FILE__, 'shortPixelDeactivatePlugin' );
}
?>