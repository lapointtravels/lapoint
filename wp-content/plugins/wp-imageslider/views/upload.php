<?php
/**
 * upload.php
 *
 * Copyright 2009, Moxiecode Systems AB
 * Released under GPL License.
 *
 * License: http://www.plupload.com/license
 * Contributing: http://www.plupload.com/contributing
 */

// HTTP headers for no cache etc
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include_once('../../../../wp-config.php');
include_once('../../../../wp-load.php');


// Fetch the slide show
$slide_show_id = $_POST["slide_show_id"];
$slide_show = $kloon_image_slider->get_slideshow($slide_show_id);


// Fetch settings and setup upload url and path
global $kloon_image_slider;
$settings = $kloon_image_slider->get_settings();
$sizes = $settings["sizes"];

$upload_dir_url = $settings["upload_dir_url"];
$targetDir = $settings["upload_dir_path"];
if ($settings["use_seperate_upload_folders"]) {
	$upload_dir_url = $upload_dir_url . $slide_show->id ."/";
	$targetDir = $targetDir ."/" . $slide_show->id;
}

if (!is_dir($targetDir)) mkdir($targetDir);

if (!$slide_show) {
	die('{"jsonrpc" : "2.0", "error" : {"code": 500, "message": "No slide show found."}, "id" : "id"}');
}

$cleanupTargetDir = true; // Remove old files
$maxFileAge = 5 * 3600; // Temp file age in seconds

// 5 minutes execution time
@set_time_limit(5 * 60);

// Uncomment this one to fake upload time
// usleep(5000);

// Get parameters
$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';

// Clean the fileName for security reasons
$fileName = preg_replace('/[^\w\._]+/', '_', $fileName);

// Make sure the fileName is unique but only if chunking is disabled
if ($chunks < 2 && file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName)) {
	$ext = strrpos($fileName, '.');
	$fileName_a = substr($fileName, 0, $ext);
	$fileName_b = substr($fileName, $ext);

	$count = 1;
	while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b))
		$count++;

	$fileName = $fileName_a . '_' . $count . $fileName_b;
}

$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

// Create target dir
if (!file_exists($targetDir))
	@mkdir($targetDir);

// Remove old temp files
if ($cleanupTargetDir && is_dir($targetDir) && ($dir = opendir($targetDir))) {
	while (($file = readdir($dir)) !== false) {
		$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

		// Remove temp file if it is older than the max age and is not the current file
		if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge) && ($tmpfilePath != "{$filePath}.part")) {
			@unlink($tmpfilePath);
		}
	}

	closedir($dir);
} else
	die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');


// Look for the content type header
if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
	$contentType = $_SERVER["HTTP_CONTENT_TYPE"];

if (isset($_SERVER["CONTENT_TYPE"]))
	$contentType = $_SERVER["CONTENT_TYPE"];

// Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
if (strpos($contentType, "multipart") !== false) {
	if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
		// Open temp file
		$out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
		if ($out) {
			// Read binary input stream and append it to temp file
			$in = fopen($_FILES['file']['tmp_name'], "rb");

			if ($in) {
				while ($buff = fread($in, 4096))
					fwrite($out, $buff);
			} else
				die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
			fclose($in);
			fclose($out);
			@unlink($_FILES['file']['tmp_name']);
		} else
			die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
	} else
		die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
} else {
	// Open temp file
	$out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
	if ($out) {
		// Read binary input stream and append it to temp file
		$in = fopen("php://input", "rb");

		if ($in) {
			while ($buff = fread($in, 4096))
				fwrite($out, $buff);
		} else
			die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');

		fclose($in);
		fclose($out);
	} else
		die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
}


// Check if file has been uploaded
if (!$chunks || $chunk == $chunks - 1) {

	// Strip the temp .part suffix off
	rename("{$filePath}.part", $filePath);

	// Check if the image is big enough
	$image_width = $_POST["min_width"];
	$image_height = $_POST["min_height"];
	$arrSize = getimagesize($filePath);


	$min_width = $sizes["md"][0];
	$min_height = $sizes["md"][1];
	if ($slide_show->is_fullscreen()) {
		$min_width = $sizes["lg"][0];
		$min_height = $sizes["lg"][1];
	}

	if ($arrSize[0] < $min_width || $arrSize[1] < $min_height){
		unlink($filePath);
		die('{"jsonrpc" : "2.0", "error" : {"code": 201, "message": "The image was too small ('. $arrSize[0] .'x'. $arrSize[1] .'px). It must atleast be '. $min_width .'x'. $min_height .'px."}, "id" : "id"}');
	} else {

		// Create thumb
		require_once(ABSPATH . '/wp-admin/includes/image.php');

		$filetype = wp_check_filetype($fileName, null);
		$fileExtension = strtolower($filetype['ext']);
		$fileNameWithoutExtension = substr($fileName, 0, strlen($fileName) - strlen($fileExtension) - 1);

		image_resize($filePath, $sizes["thumb"][0], $sizes["thumb"][1], true, "thumb");
		if ($slide_show->meta["size"][0] === "fixed") {
			image_resize($filePath, $sizes["lg"][0], $sizes["lg"][1], true, "lg");
			image_resize($filePath, $sizes["md"][0], $sizes["md"][1], true, "md");
			image_resize($filePath, $sizes["sm"][0], $sizes["sm"][1], true, "sm");
			image_resize($filePath, $sizes["xs"][0], $sizes["xs"][1], true, "xs");
		} else {
			image_resize($filePath, $sizes["lg"][0], 99999, false, "lg");
			image_resize($filePath, $sizes["md"][0], 99999, false, "md");
			image_resize($filePath, $sizes["sm"][0], 99999, false, "sm");
			image_resize($filePath, $sizes["xs"][0], 99999, false, "xs");
		}

		// Save in db
		include_once('../../../../wp-includes/wp-db.php');

	    global $wpdb;
		$wpdb->query(sprintf("INSERT INTO %s (`slide_show_id` , `filename` , `dirname` , `url` , `title` , `type` , `mime` , `width` , `height`  , `created` , `updated`) VALUES ('%s', '%s', '%s', '%s', '', '%s', '%s', '%s', '%s', NOW(), NOW());",
			Kloon_Image_Slider::get_db_slides_table(),
			$slide_show->id,
			$fileNameWithoutExtension,
			$targetDir,
			$filePath,
			$fileExtension,
			$filetype['type'],
			$arrSize[0],
			$arrSize[1]
		));

		// Get the id
		$result = $wpdb->get_results(sprintf("SELECT id FROM `%s` WHERE slide_show_id='%s' AND filename='%s' ORDER BY id DESC LIMIT 1;",
			Kloon_Image_Slider::get_db_slides_table(),
			$slide_show->id,
			$fileNameWithoutExtension
		));

		die('{"jsonrpc" : "2.0", "result" : null, "id" : "'. $result[0]->id .'", "thumb_url" : "'. $upload_dir_url . $fileNameWithoutExtension .'-thumb.'. $fileExtension .'", "filename": "'. $fileNameWithoutExtension .'", "type": "'. $fileExtension .'"}');

	}

}

// Return JSON-RPC response
die('{"jsonrpc" : "2.0", "result" : null}');

?>