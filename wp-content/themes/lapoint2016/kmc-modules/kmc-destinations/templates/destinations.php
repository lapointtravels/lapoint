<?php
global $DESTINATION_TYPE, $destinations_manager;
$destinations = $DESTINATION_TYPE->get_destinations();

$tag = (isset($this->tag) && $this->tag == 'h1') ? 'h1' : 'h2';
?>

<?php if ($this->full_width) : ?>
	<div>
<?php else : ?>
	<div class="container">
<?php endif; ?>

	<header>
		<?php if ($this->post->post_title) : ?>
			<<?php echo $tag; ?>><?php echo $this->post->post_title; ?></<?php echo $tag; ?>>
		<?php endif; ?>

		<?php if ($this->post->post_content) : ?>
			<div class="ingress wide">
				<p><?php echo $this->post->post_content; ?></p>
			</div>
		<?php endif; ?>
	</header>


	<div class="destinations-container row row-tight">
		<?php
		$i = 0;
		foreach ($destinations as $destination):
			$i++;
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

			if ($image_info):
				$col_sm = (6 * $image_info->width_sm);
				$col_md = (4 * $image_info->width_md);
				$height_md = $image_info->height_md;
				$height_sm = $image_info->height_sm;
			else:
				$col_sm = (6 * $box_info->width_sm);
				$col_md = (4 * $box_info->width_md);
				$height_md = $box_info->height_md;
				$height_sm = $box_info->height_sm;
			endif;

			?>
			<div class="preview-box destination col-sm-<?php echo $col_sm; ?> col-md-<?php echo $col_md; ?> mbs <?php if ($height_md == 2) echo "row-span-md"; ?> <?php if ($height_sm == 2) echo "row-span-sm"; ?>" data-animated="slide-up-fade" data-delay="<?php echo $i * 150; ?>">
				<div class="inner responsive-image"<?php
					if ($image_info):
						echo " style=\"background-image: url('". $image_info->background_image["sizes"]["box-sm"] . "');\"";

						$src_md = ($image_info->height_md > 1) ? "box-tall-" : "box-";
						$src_sm = ($image_info->height_sm > 1) ? "box-tall-" : "box-";
						$src_md .= ($image_info->width_md > 1) ? "md" : "sm";
						$src_sm .= ($image_info->width_sm > 1) ? "md" : "sm";

						echo " data-src-md='". $image_info->background_image["sizes"][$src_md] ."'";
						echo " data-src-sm='". $image_info->background_image["sizes"][$src_sm] ."'";
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
		<?php endforeach ?>
	</div>

</div>
