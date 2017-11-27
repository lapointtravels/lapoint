<?php

function render_single_destination_box ($id) {
	global $DESTINATION_TYPE, $destinations_manager;
	$destination = $destinations_manager->get($id);

	$box_info = $destination->get_box_info();

	$image_info = false;
	if ($box_info->background_image) :
		$image_info = $box_info;
	else:
		// Check if the swedish translation has a background image
		if (ICL_LANGUAGE_CODE != "sv") :
			$swe_id = apply_filters("wpml_object_id", $destination->id, "destination", FALSE, "sv");
			if ($swe_id):
				$swe_destination = $destinations_manager->get($swe_id);
				$swe_box_info = $swe_destination->get_box_info();
				if ($swe_box_info->background_image) :
					$image_info = $swe_box_info;
				endif;
			endif;
		endif;
	endif;
	?>

	<div class="preview-box destination mtl" data-animated="slide-up-fade" data-delay="<?php echo $i * 150; ?>" style="max-width: 777px; margin: 0 auto;">
		<div class="inner responsive-image"<?php
			if ($image_info):
				echo " style=\"background-image: url('". $image_info->background_image["sizes"]["box-sm"] . "');\"";
				echo " data-src-md='". $image_info->background_image["sizes"]["box-md"] ."'";
				echo " data-src-sm='". $image_info->background_image["sizes"]["box-sm"] ."'";
				echo " data-src-xs='". $image_info->background_image["sizes"]["box-md"] ."'";

			else:

				echo " style='background-color: #222;'";
			endif;
		?>>
			<h4><?php echo $destination->title; ?></h4>

			<div class="overlay">
				<div>
					<a href="<?php echo $destination->link; ?>" class="btn btn-inverted"><?php
					if ($box_info->button_text == "DEFAULT" || !$box_info->button_text) :
						echo __("Show", "lapoint");
					else:
						echo $box_info->button_text;
					endif;
					?></a>
				</div>
			</div>
		</div>

	</div>

	<?php
}
