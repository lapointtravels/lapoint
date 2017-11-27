<?php
if (!function_exists('assert_admin_access')) :
	function assert_admin_access(){
		if (!current_user_can('manage_options'))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
	}
endif;


if (!function_exists('assert_numeric_get')) :
	function assert_numeric_get($key){
		if (!isset($_GET[$key]) || !is_numeric($_GET[$key])){
			wp_die( __('Incorrect indata.') );
		}
		return $_GET[$key];
	}
endif;


if (!function_exists('assert_numeric_post')) :
	function assert_numeric_post($key){
		if (!isset($_POST[$key]) || !is_numeric($_POST[$key])){
			wp_die( __('Incorrect indata.') );
		}
		return $_POST[$key];
	}
endif;


if (!function_exists('is_admin_action')) :
	function is_admin_action($action){
		return (isset($_POST['admin-action']) && $_POST['admin-action'] == $action);
	}
endif;


if (!function_exists('delete_file')) :
	function delete_file($filename){
		if (file_exists($filename)) unlink($filename);
	}
endif;

if (!function_exists('json_reponse')) :
	function json_reponse($output){
		header('Content-Type: application/json');
		echo json_encode($output);
		die();
	}
endif;
