<?php
$slides = $this->get_slides();
?>

<div class="kloonslides-slideshow <?php
	if ($this->is_fixed_height) :
		echo " fixed-height";
	elseif ($this->is_fullscreen) :
		echo " fullscreen";
	endif;
	?>"
	data-slideshow-id="<?php echo $this->id; ?>"
	data-timer="<?php echo $this->timer; ?>"

	<?php if ($this->is_fixed_height) : ?>
		data-height-desktop="<?php echo $this->fixed_height_px; ?>"
		data-height-tablet="<?php echo $this->fixed_height_tablet; ?>"
		data-height-phone="<?php echo $this->fixed_height_phone; ?>"
	<?php endif; ?>

	<?php if ($this->is_fixed_height && $this->fixed_height_px) : ?>
		style="height: <?php echo $this->fixed_height_px; ?>px;"
	<?php endif; ?>
	>

	<ul class="slides-container" style="width: <?php echo sizeof($slides) * 200; ?>%; <?php
	if ($this->is_fixed_height && $this->fixed_height_px) :
		echo " height: " . $this->fixed_height_px . "px;";
	endif;
	?> transform: translate3d(0, 0, 0);">
		<?php foreach ($slides as $slide) :
			$presentation_object = $slide->get_presentation_object();
			$presentation_display_data = $slide->get_presentation_display_data();
			$classes = $presentation_display_data->classes;
			$presentation_output = $presentation_display_data->presentation_output;

			?>

			<?php if ($slide->is_youtube) : ?>

				<li data-slide-id="<?php echo $slide->id ?>"
					data-type="video"
					data-video-type="youtube"
					data-has-bgr-video="<?php echo $slide->has_bgr_video; ?>"
					data-width="<?php echo $slide->data->width; ?>"
					data-height="<?php echo $slide->data->height; ?>"
					data-youtube-id="<?php echo $slide->data->video_id; ?>"
					data-autoplay="<?php echo $slide->data->autoplay; ?>"
					data-keep-proportions="<?php echo $slide->data->keep_proportions; ?>"
					class="slide youtube-slide presentation-<?php echo $slide->presentation; ?> <?php echo $classes; ?>"
					>

					<div class="presentations">
						<?php echo $presentation_output; ?>

						<?php if ($slide->has_bgr_video) : ?>
							<div class="play-video-icon-wrapper">
								<i class="play-video-icon"></i>
							</div>
						<?php endif; ?>
					</div>

					<?php if ($slide->has_bgr_video) : ?>
						<div class="bgr-video">
							<video loop muted>
								<?php if ($slide->data->background_video_ogv) : ?>
									<source src="<?php echo $slide->data->background_video_ogv->url; ?>" type="video/ogv">
								<?php endif; ?>
								<?php if ($slide->data->background_video_mp4) : ?>
									<source src="<?php echo $slide->data->background_video_mp4->url; ?>" type="video/mp4">
								<?php endif; ?>
							</video>
						</div>

					<?php else: ?>
						<div class="video-container"></div>
					<?php endif; ?>
				</li>

			<?php else : ?>

				<li data-slide-id="<?php echo $slide->id ?>"
					data-type="image"
					<?php
					$image_lg = $slide->get_image("lg");
					$image_md = $slide->get_image("md");
					$image_sm = $slide->get_image("sm");
					?>
					data-lg-src="<?php echo $image_lg->url; ?>"
					data-lg-width="<?php echo $image_lg->width; ?>"
					data-lg-height="<?php echo $image_lg->height; ?>"
					data-md-src="<?php echo $image_md->url; ?>"
					data-md-width="<?php echo $image_md->width; ?>"
					data-md-height="<?php echo $image_md->height; ?>"
					data-sm-src="<?php echo $image_sm->url; ?>"
					data-sm-width="<?php echo $image_sm->width; ?>"
					data-sm-height="<?php echo $image_sm->height; ?>"
					class="slide presentation-<?php echo $slide->presentation; ?> <?php echo $classes; ?>"
					>

					<div class="presentations">
						<?php echo $presentation_output; ?>
					</div>

					<div class="image" <?php if ($slide->vertical_align) : ?>style="background-position-y: <?php echo $slide->vertical_align; ?> !important;"<?php endif; ?>></div>

					<?php if ($slide->link) : ?>
						</a>
					<?php endif; ?>
				</li>

			<?php endif; ?>

		<?php endforeach; ?>
	</ul>

	<?php if (sizeof($slides) > 1) : ?>
		<?php if (!$this->hide_nav) : ?>
			<a href="#" class="prev-link icon-link"><i class="prev-icon icon"></i></a>
			<a href="#" class="next-link icon-link"><i class="next-icon icon"></i></a>
		<?php endif; ?>

		<!-- Dots -->
		<ul class="kloonslides-dots">
			<?php
			$first = true;
			$position = 0;
			foreach ($slides as $slide){ ?>
				<li
					class="dot <?php
						if ($first) :
							echo "active";
							$first = false;
						endif;
					?>"
					data-slide-id="<?php echo $slide->id ?>"
					data-slide-position="<?php echo $position++ ?>"
				></li>
			<?php } ?>
		</ul>
	<?php endif; ?>
</div>
