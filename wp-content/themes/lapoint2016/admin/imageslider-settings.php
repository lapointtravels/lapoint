<?php
global $IMAGE_SLIDER_SETTINGS;
$IMAGE_SLIDER_SETTINGS = array(
	"upload_dir_path" => wp_upload_dir()['basedir'] .'/imsl',
	//"upload_dir_path" => "/home/d25504/public_html/clients/lapoint/2016/wp-content/uploads/imsl",
	//"upload_dir_url" => "http://www.lapointcamps.com/wp-content/uploads/imsl/",
	"upload_dir_url" =>  wp_upload_dir()['baseurl'] ."/imsl/",

	//"upload_dir_path" => "/home/d25504/public_html/clients/ikonhus/wp-content/uploads/imsl",
	//"upload_dir_url" => "http://kloon.se/clients/ikonhus/wp-content/uploads/imsl/",
	//"exclude_setting" => array("fixed_height"),
	"extra_class" => "simple",
	"slide_types" => array(
		array(
			"id" => 1,
			"label" => "Enbart bild",
			"class" => "slide-type-1",
			"choices" => array(),
			"link" => false,
			"title" => false
		),
		array(
			"id" => 2,
			"label" => "Rubrik",
			"class" => "slide-type-2",
			"choices" => array(),
			"link" => false,
			"title" => true
		),
		array(
			"id" => 3,
			"label" => "Text + knapp (vänsterställd)",
			"class" => "slide-type-3",
			"choices" => array("Text"),
			"link" => false,
			"title" => true
		)
	)
);
