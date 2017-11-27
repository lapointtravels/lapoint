<?php

class Set_Meta_Helper {

	public function __construct ($setMeta, $JSON_MAP) {
		$this->setMeta = $setMeta;
		$this->JSON_MAP = $JSON_MAP;

		//var_dump($this->JSON_MAP);
	}


	public function get_post_id_by_title_and_lang ($title, $post_type, $lang) {
		global $wpdb;
		$post_id = $wpdb->get_var(sprintf("
			SELECT wp.ID
			FROM %s AS wp
			INNER JOIN %s as wpml ON wp.ID=wpml.element_id
			WHERE wp.post_title='%s' AND wp.post_type='%s' AND wp.post_status='publish' AND wpml.language_code='%s'",
			$wpdb->posts, $wpdb->prefix . "icl_translations", $title, $post_type, $lang)
		);
		return $post_id;
	}
	public function get_post_in_all_language ($sv_id, $post_type) {
		$en_id = icl_object_id($sv_id, $post_type, false, "en");
		$da_id = icl_object_id($sv_id, $post_type, false, "da");
		$nb_id = icl_object_id($sv_id, $post_type, false, "nb");
		return array(
			"sv" => get_post($sv_id),
			"en" => get_post($en_id),
			"da" => get_post($da_id),
			"nb" => get_post($nb_id)
		);
	}
	public function get_post_in_all_language_by_title_and_lang ($title, $post_type, $lang) {
		$sv_id = $this->get_post_id_by_title_and_lang($title, $post_type, $lang);
		return $this->get_post_in_all_language($sv_id, $post_type);
	}

	public function set_h1 ($obj, $text) {
		$obj_id = $obj->id ? $obj->id : $obj->ID;
		//echo "<br>set h1: ". $text . " (". $obj_id .")<br>";
		$meta_data = get_post_meta($obj_id, 'kmc_page_components', true);
		$sections = json_decode($meta_data);


		if (count($sections) > 1) :
			$components = $sections[0]->components;
			if (count($components) > 0) :
				$component = get_post($components[0]);
				if ($component->post_type == "info-box") :
					$info_box = new KMC_Info_Box_Component($component);
					$info_box->post->post_title = $text;

					wp_update_post(array(
	      				'ID' => $info_box->id,
	      				'post_title' => $text
	      			));

				else:
					echo "Warning - First component is not a info box";

				endif;
			else:
				echo "Warning - No components";
			endif;
		else:
			echo "Warning - No sections";
		endif;
	}


	# ****************************** Misc pages ******************************
	public function meta_fix_specific_pages () {

		$lang_posts = array(
			"surfcamp" => $this->get_post_in_all_language_by_title_and_lang("Surf Camp", "destination-type", "sv"),
			"kitecamp" => $this->get_post_in_all_language_by_title_and_lang("Kite Camp", "destination-type", "sv"),
			"youthcamp" => $this->get_post_in_all_language_by_title_and_lang("Youth Camp", "destination-type", "sv"),
			"surfcamp_destinations" => $this->get_post_in_all_language_by_title_and_lang("Destinations key", "page", "sv"),
			"kitecamps_destinations" => $this->get_post_in_all_language_by_title_and_lang("Kitecamps destinations", "page", "sv"),
		);

		foreach ($lang_posts as $key => $langs) :
			echo "*** " . $key . " ***<br>";
			foreach ($langs as $code => $post) :
				echo $code .": " . $post->ID ." : ";
				$title = $this->JSON_MAP->misc->$key->$code->title;
				echo ", TITLE: ";
				if ($title) :
					echo $title;
					if ($this->setMeta):
						echo " (*)";
						update_post_meta($post->ID, '_amt_title', $title);
					endif;
				else:
					echo " -";
				endif;

				$h1 = $this->JSON_MAP->misc->$key->$code->h1;
				echo ", H1: ";
				if ($h1) :
					echo $h1;
					if ($this->setMeta):
						$this->set_h1($post, $h1);
						echo " (*)";
						//update_post_meta($post->ID, '_amt_title', $h1);
					endif;
				else:
					echo " -";
				endif;
				echo "<br>";

			endforeach;
		endforeach;
	}


	# ****************************** Packages ******************************
	public function meta_fix_packages () {

		echo "Packages:<br>";
		global $packages_manager;
		$packages = $packages_manager->get_all_all_lang();
		foreach ($packages as $package) {
			$code = $package->get_lang_code();
			echo $package->id .": ". $package->title ." (". $code .") : ";

			$dest_type = $package->get_destination()->get_type();
			$dest_type_title = strtolower($dest_type->title);
			$type = "";
			if (strpos($dest_type_title, "surf") !== false) {
				$type = "surf";
			} else if (strpos($dest_type_title, "kite") !== false) {
				$type = "kite";
			}


			// Get the english title
			if ($code == "en") {
				$en_package = $package;
			} else {
				$en_id = icl_object_id($package->id, "package", false, "en");
				$en_package = $packages_manager->get($en_id);
			}
			$en_package_title = $en_package->title;


			if (strpos($en_package_title, "Level 1") !== false) {
				$level_code = "level1";
			} else if (strpos($en_package_title, "Level 2") !== false) {
				$level_code = "level2";
			} else if (strpos($en_package_title, "Level 3") !== false) {
				$level_code = "level3";
			} else if (strpos($en_package_title, "Basic") !== false) {
				$level_code = "basic";
			} else if (strpos($en_package_title, "Girlcamp") !== false) {
				$level_code = "girlcamp";
			} else if (strpos($en_package_title, "Winter camp") !== false) {
				$level_code = "wintercamp";
			} else if (strpos($en_package_title, "Coaching - Advanced") !== false) {
				$level_code = "coaching_advanced";
			} else if (strpos($en_package_title, "Coaching - Intermediate") !== false) {
				$level_code = "coaching_intermediate";
			} else if (strpos($en_package_title, "Coaching - Elite") !== false) {
				$level_code = "coaching_elite";
			} else if (strpos($en_package_title, "Coaching") !== false) {
				$level_code = "coaching";
			} else {
				echo "MISSING: ". $en_package_title ." (". $package->title .")<br>";
			}

			if ($type && $level_code && isset($this->JSON_MAP->packages->$type->$level_code) && isset($this->JSON_MAP->packages->$type->$level_code->$code)) {
				$title = $this->JSON_MAP->packages->$type->$level_code->$code->title;
				$title = str_replace("[DESTINATION]", $package->get_destination()->title, $title);
				if ($title) :
					echo $title;
					if ($this->setMeta):
						echo " (*)";
						update_post_meta($package->id, '_amt_title', $title);
					endif;
				endif;


				$h1 = $this->JSON_MAP->packages->$type->$level_code->$code->h1;
				$h1 = str_replace("[DESTINATION]", $package->get_destination()->title, $h1);
				echo ", H1: ";
				if ($h1) :
					echo $h1;
					if ($this->setMeta):
						$this->set_h1($package, $h1);
						echo " (*)";
					endif;
				else:
					echo " -";
				endif;
			}


			/*if (isset($DEST_TITLE[$code])) {
				update_post_meta($package->id, '_amt_title', $DEST_TITLE[$code]);
				echo $DEST_TITLE[$code];
			}*/
			echo "<br>";
		}
		echo "<br><br><br>";
	}



	# ****************************** Levels ******************************
	public function meta_fix_levels () {
		echo "Levels:<br>";
		global $levels_manager;
		$levels = $levels_manager->get_all_all_lang();
		foreach ($levels as $level) {
			$code = $level->get_lang_code();
			echo $level->id .": ". $level->title ." (". $code .") : ";

			$dest_type = $level->get_type();
			$dest_type_title = strtolower($dest_type->title);
			$type = "";
			if (strpos($dest_type_title, "surf") !== false) {
				$type = "surf";
			} else if (strpos($dest_type_title, "kite") !== false) {
				$type = "kite";
			}


			// Get the english title
			if ($code == "en") {
				$en_level = $level;
			} else {
				$en_id = icl_object_id($level->id, "level", false, "en");
				$en_level = $levels_manager->get($en_id);
			}
			$en_level_title = $en_level->title;

			$level_code = false;
			if (strpos($en_level_title, "Level 1") !== false) {
				$level_code = "level1";
			} else if (strpos($en_level_title, "Level 2") !== false) {
				$level_code = "level2";
			} else if (strpos($en_level_title, "Level 3") !== false) {
				$level_code = "level3";
			} else if (strpos($en_level_title, "Basic") !== false) {
				$level_code = "basic";
			//} else if (strpos($en_level_title, "Girlcamp") !== false) {
			//	$level_code = "girlcamp";
			//} else if (strpos($en_level_title, "Winter camp") !== false) {
			//	$level_code = "wintercamp";
			} else if (strpos($en_level_title, "Advanced") !== false) {
				$level_code = "coaching_advanced";
			} else if (strpos($en_level_title, "Intermediate") !== false) {
				$level_code = "coaching_intermediate";
			} else if (strpos($en_level_title, "Elite") !== false) {
				$level_code = "coaching_elite";
			} else if (strpos($en_level_title, "Coaching") !== false) {
				$level_code = "coaching";
			} else {
				echo "MISSING: ". $en_level_title ." (". $level->title .")";
			}

			if ($type && $level_code && isset($this->JSON_MAP->levels->$type->$level_code) && isset($this->JSON_MAP->levels->$type->$level_code->$code)) {
				$title = $this->JSON_MAP->levels->$type->$level_code->$code->title;
				if ($title) :
					echo $title;
					if ($this->setMeta):
						echo " (*)";
						update_post_meta($level->id, '_amt_title', $title);
					endif;
				endif;


				$h1 = $this->JSON_MAP->levels->$type->$level_code->$code->h1;
				echo ", H1: ";
				if ($this->setMeta && $h1) :
					echo $h1;
					if ($this->setMeta):
						$this->set_h1($level, $h1);
						echo " (*)";
					endif;
				else:
					echo " -";
				endif;
			}


			/*if (isset($DEST_TITLE[$code])) {
				update_post_meta($package->id, '_amt_title', $DEST_TITLE[$code]);
				echo $DEST_TITLE[$code];
			}*/
			echo "<br>";
		}
		echo "<br><br><br>";
	}

	# ****************************** Destinations ******************************
	public function meta_fix_destinations () {
		global $destinations_manager;
		$destinations = $destinations_manager->get_all_all_lang();

		foreach ($destinations as $destination) :
			$code = $destination->get_lang_code();
			echo $destination->id .": ". $destination->title ." (". $code .") : ";

			$dest_type = $destination->get_type();
			$dest_type_title = strtolower($dest_type->title);
			$type = "";
			if (strpos($dest_type_title, "surf") !== false) :
				$type = "surf";
			elseif (strpos($dest_type_title, "kite") !== false) :
				$type = "kite";
			elseif (strpos($dest_type_title, "youth") !== false) :
				$type = "youth";
			endif;


			if ($type && $code && isset($this->JSON_MAP->destinations->$type->$code)) {

				// Title
				$title = $this->JSON_MAP->destinations->$type->$code->title;
				$title = str_replace("[DESTINATION]", $destination->title, $title);
				if ($this->setMeta):
					echo " (*)";
					update_post_meta($destination->id, '_amt_title', $title);
				endif;
				echo $title;

				// H1
				$h1 = $this->JSON_MAP->destinations->$type->$code->h1;
				$h1 = str_replace("[DESTINATION]", $destination->title, $h1);
				echo ", H1: ";
				if ($this->setMeta):
					echo " (*)";
					$this->set_h1($destination, $h1);
				endif;
				echo "!" . $h1 ."!";

			} else {
				echo "--- (". $type ." | ". $code .")";
			}

			echo "<br>";
		endforeach;
	}


	# ****************************** Camps ******************************
	public function meta_fix_camps () {
		global $camps_manager;
		$camps = $camps_manager->get_all_all_lang();

		foreach ($camps as $camp) :
			$code = $camp->get_lang_code();
			echo $camp->id .": ". $camp->title ." (". $code .") : ";

			$dest_type = $camp->get_destination()->get_type();
			$dest_type_title = strtolower($dest_type->title);
			$type = "";
			if (strpos($dest_type_title, "surf") !== false) :
				$type = "surf";
			elseif (strpos($dest_type_title, "kite") !== false) :
				$type = "kite";
			endif;


			if ($type && $code && isset($this->JSON_MAP->camps->$type->$code)) {

				// Title
				$title = $this->JSON_MAP->camps->$type->$code->title;
				$title = str_replace("[DESTINATION]", $camp->get_destination()->title, $title);
				$title = str_replace("[ACCOMODATION]", $camp->title, $title);
				if ($camp->get_location()) :
					$title = str_replace("[LOCATION]", ", " . $camp->get_location()->display_label, $title);
				else :
					$title = str_replace("[LOCATION]", "", $title);
				endif;
				if ($this->setMeta):
					echo " (*)";
					update_post_meta($camp->id, '_amt_title', $title);
				endif;
				echo $title;

				// H1
				$h1 = $this->JSON_MAP->camps->$type->$code->h1;
				$h1 = str_replace("[DESTINATION]", $camp->get_destination()->title, $h1);
				$h1 = str_replace("[ACCOMODATION]", $camp->title, $h1);
				echo ", H1: ";
				if ($camp->get_location()) :
					$h1 = str_replace("[LOCATION]", ", " . $camp->get_location()->display_label, $h1);
				else :
					$h1 = str_replace("[LOCATION]", "", $h1);
				endif;
				if ($this->setMeta):
					echo " (*)";
					$this->set_h1($camp, $h1);
				endif;
				echo $h1;


			} else {
				echo "--- (". $type ." | ". $code .")";
			}

			echo "<br>";
		endforeach;
	}

}
