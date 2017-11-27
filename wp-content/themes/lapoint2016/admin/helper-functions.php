<?php

if (!function_exists('lapoint_header_image')) :

	function lapoint_header_image () {

		if (has_post_thumbnail()) :
			echo '<div class="full-image">';
			the_post_thumbnail("header-image");
			echo '</div>';
		endif;

	}

endif;


function get_wpml_home_url ($permalink) {

	if (strpos($permalink, "localhost")) {
		$homeUrl = WPML_HOME_URI;
		if (strpos($permalink, "/da/?")) {
        	$homeUrl = WPML_HOME_URI . "/da";
        } else if (strpos($permalink, "/nb/?")) {
        	$homeUrl = WPML_HOME_URI . "/nb";
        } else if (strpos($permalink, "/sv/?")) {
        	$homeUrl = WPML_HOME_URI . "/sv";
        }
	} else {
	    $homeUrl = "lapointcamps.com";
	    if (strpos($permalink, "lapoint.dk")) {
	    	$homeUrl = "lapoint.dk";
	    } else if (strpos($permalink, "lapoint.no")) {
	    	$homeUrl = "lapoint.no";
	    } else if (strpos($permalink, "lapoint.se")) {
	    	$homeUrl = "lapoint.se";
	    }
	    $homeUrl = "https://www." . $homeUrl . "/";
	}

	return $homeUrl;
}
