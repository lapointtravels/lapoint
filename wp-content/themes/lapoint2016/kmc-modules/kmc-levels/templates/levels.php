<?php
global $LEVEL, $DESTINATION_TYPE, $levels_manager;

if ($LEVEL) :
	$levels = $LEVEL->get_levels();
else :
	$levels = $DESTINATION_TYPE->get_levels();
endif;
?>

	<?php if ($this->full_width) : ?>
		<div>
	<?php else : ?>
		<div class="container">
	<?php endif; ?>

		<header>
			<?php if ($this->post->post_title) : ?>
				<h2><?php echo $this->post->post_title; ?></h2>
			<?php endif; ?>

			<?php if ($this->post->post_content) : ?>
				<div class="ingress wide">
					<p><?php echo $this->post->post_content; ?></p>
				</div>
			<?php endif; ?>
		</header>


		<div class="levels-container row row-tight">
			<?php
			$i = 0;
			foreach ($levels as $level):
				$i++;
				$box_info = $level->get_box_info();

				$image_info = false;
				if ($box_info->background_image) :
					$image_info = $box_info;
				else:
					// Check if the swedish translation has a background image
					if (ICL_LANGUAGE_CODE != "sv") :
						$swe_id = apply_filters("wpml_object_id", $level->id, "level", FALSE, "sv");
						if ($swe_id):
							$swe_level = $levels_manager->get($swe_id);
							$swe_box_info = $swe_level->get_box_info();
							if ($swe_box_info->background_image) :
								$image_info = $swe_box_info;
							endif;
						endif;
					endif;
				endif;

				if ($image_info):
					$col_sm = $image_info->width_sm;
					$col_md = $image_info->width_md;
					$height_md = $image_info->height_md;
					$height_sm = $image_info->height_sm;
				else:
					$col_sm = $box_info->width_sm;
					$col_md = $box_info->width_md;
					$height_md = $box_info->height_md;
					$height_sm = $box_info->height_sm;
				endif;
				?>
				<div class="preview-box level col-sm-<?php echo $col_sm; ?> col-md-<?php echo $col_md; ?> mbs <?php if ($height_md == 2) echo "row-span-md"; ?> <?php if ($height_sm == 2) echo "row-span-sm"; ?>" data-animated="slide-up-fade" data-delay="<?php echo $i * 150; ?>">
					<div class="inner responsive-image"<?php

					if ($image_info):
						echo " style=\"background-image: url('". $image_info->background_image["sizes"]["box-sm"] . "');\"";

						$src_md = ($image_info->height_md > 1) ? "box-tall-" : "box-";
						$src_sm = ($image_info->height_sm > 1) ? "box-tall-" : "box-";
						$src_md .= ($image_info->width_md > 4) ? "md" : "sm";
						$src_sm .= ($image_info->width_sm > 4) ? "md" : "sm";

						echo " data-src-md='". $image_info->background_image["sizes"][$src_md] ."'";
						echo " data-src-sm='". $image_info->background_image["sizes"][$src_sm] ."'";
						echo " data-src-xs='". $image_info->background_image["sizes"]["box-md"] ."'";
					else:
						echo " style='background-color: #222;'";
					endif;
					?>>
						<h4><?php echo $level->display_label; ?></h4>

						<a href="<?php echo $level->link; ?>">
							<div class="overlay">
								<div>
									<span class="btn btn-inverted"><?php
									if ($box_info->button_text == "DEFAULT" || !$box_info->button_text) :
										echo __("Show", "lapoint");
									else:
										echo $box_info->button_text;
									endif;
									?></span>
								</div>
							</div>
						</a>
					</div>

				</div>
			<?php endforeach ?>
		</div>

	</div>
