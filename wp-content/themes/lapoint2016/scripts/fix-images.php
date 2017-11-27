<?php

class Fix_Image_Helper {

	public function __construct ($dry_run, $debug) {
		$this->dry_run = $dry_run;
		$this->debug = $debug;
	}

	public function fix_section_images ($sections) {
		global $wpdb;

		foreach ($sections as $section) {

			if ($section->settings->background_image) {

				if (!$section->settings->background_image->image_2500 && !$section->settings->background_image->image_1200 && !$section->settings->background_image->image_770) {

					//echo "<br>HAS IMAGE: ". $section->settings->background_image->url ."<br>";
					//echo "Full: ". $section->settings->background_image->full ."<br>";
					$last = explode("/", $section->settings->background_image->url);
					$last = array_slice($last, -1);
					$last = $last[0];
					echo "Last: ". $last ."<br>";

					$image = $wpdb->get_results('SELECT * FROM ' . $wpdb->postmeta  . ' WHERE meta_value LIKE \'%"' . $last . '"%\';');
					if (count($image) == 0) {
						echo "<span style='color: red;'>WAAAAAARNINGGGG!!!!!!!!!!</span><br>";
					} else {
						if (count($image) > 1) {
							echo "<span style='color: yellow;'>Found more that one image!</span><br>";
						}
						$image = $image[0];
						$img2500 = wp_get_attachment_image_src($image->post_id, "header-image");
						$img2500 = $img2500[0];

						$img1200 = wp_get_attachment_image_src($image->post_id, "himage-1200");
						$img1200 = $img1200[0];

						$img770 = wp_get_attachment_image_src($image->post_id, "image-770");
						$img770 = $img770[0];

						$section->settings->background_image->id = $image->post_id;
						$section->settings->background_image->image_2500 = $img2500;
						$section->settings->background_image->image_1200 = $img1200;
						$section->settings->background_image->image_770 = $img770;

						echo "image-2500: ". $img2500 ."<br>";
						echo "image-1200: ". $img1200 ."<br>";
						echo "image-770: ". $img770 ."<br>";
					}

				} else {
					echo "Already fixed<br>";
				}
			}
		}

		return $sections;
	}

	public function compare_data ($id, $sections) {
		echo "CURRENT DATA:<br>";
		var_dump(get_post_meta($id, 'kmc_page_components', true));
		echo "<br><br>NEW DATA:<br>";
		echo stripslashes(json_encode($sections));
	}

	public function fix_images_for_custom_posts ($posts) {
		foreach ($posts as $post) {
			echo "************* ". $post->title ." - " . $post->id ." *************<br>";
			$sections = json_decode(get_post_meta($post->id, 'kmc_page_components', true));
			$sections = $this->fix_section_images($sections);

			if ($this->debug) {
				$this->compare_data($post->id, $sections);
			}

			if (!$this->dry_run) {
				echo "GOOGOGO<br>";
				update_post_meta($post->id, 'kmc_page_components', stripslashes(json_encode($sections)));
			}

			echo "<br><hr><br>";
		}
	}

}

$dry_run = false;
$debug = false;

$fix_images = new Fix_Image_Helper($dry_run, $debug);

global $destination_types_manager, $destinations_manager, $levels_manager, $camps_manager;

$destination_types = $destination_types_manager->get_all_all_lang();
$fix_images->fix_images_for_custom_posts($destination_types);

$destinations = $destinations_manager->get_all_all_lang();
$fix_images->fix_images_for_custom_posts($destinations);

$levels = $levels_manager->get_all_all_lang();
$fix_images->fix_images_for_custom_posts($levels);

$camps = $camps_manager->get_all_all_lang();
$fix_images->fix_images_for_custom_posts($camps);

exit();