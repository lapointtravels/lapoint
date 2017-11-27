<?php

class Set_Canonical_Helper {

	public function __construct ($dry_run) {
		$this->dry_run = $dry_run;
	}

	public function set_for_lang ($lang) {
		$JSON = json_decode(file_get_contents(THEME_DIR ."/json/set-canonical-". $lang .".json", 0, null, null));
		//var_dump($JSON);

		foreach ($JSON->data as $data) :
			//echo var_dump($data) . "<br><br>";

			if ($data->title && $data->canfrom && $data->canto) :
				echo "Search for ". $data->title ."<br>";
				$posts = get_posts(array(
					'posts_per_page' => -1,
					'name' => $data->title,
					'post_status' => 'publish',
					'suppress_filters' => 1
				));
				if (count($posts) === 1) :
					$post = $posts[0];

					$post_language_details = apply_filters( 'wpml_post_language_details', NULL, $post->ID );
					$code = $post_language_details["language_code"];
					if ($code != $lang) :
						echo "LANG: " . $code ." - id: ". $post->ID ."<br>";
						$lang_post_id = icl_object_id($post->ID, "post", false, $lang);
						echo "NEW ID: ". $lang_post_id ."<br>";
						$post = get_post($lang_post_id);
					endif;

					echo $post->ID .": ". $post->post_title .": ". $data->canto;

					if (!$this->dry_run) :
						echo " ***";
						update_field('rel_canonical', $data->canto, $post->ID);
					endif;

					echo "<br>";

				else:
					echo "<span color='red'>WARNING!!!</span><br>";
					echo "Search for '". $data->title ."' returned '". count($posts) ."' hits!<br>";
				endif;
				echo "<br>";
			endif;
		endforeach;

	}

}


$dry_run = false;

$helper = new Set_Canonical_Helper($dry_run);

//$helper->set_for_lang("sv");
$helper->set_for_lang("da");
//$helper->set_for_lang("nb");

exit();