<?php
global $kloon_image_slider;
$settings = $kloon_image_slider->get_settings();
$sizes = $settings["sizes"];
$slides = $slide_show->get_slides();

?>

<script>
window.KloonImageSliderSizesOrder = <?php echo json_encode($settings["sizes_order"]); ?>;
if (!window.KloonImageSliderSizes) {
	window.KloonImageSliderSizes = {
		lg: [<?php echo $sizes["lg"][0] . ", " . $sizes["lg"][1]; ?>],
		md: [<?php echo $sizes["md"][0] . ", " . $sizes["md"][1]; ?>],
		sm: [<?php echo $sizes["sm"][0] . ", " . $sizes["sm"][1]; ?>],
		//xs: [<?php echo $sizes["xs"][0] . ", " . $sizes["xs"][1]; ?>],
		thumb: [<?php echo $sizes["thumb"][0] . ", " . $sizes["thumb"][1]; ?>]
	}
}
</script>

<div class="imsl-slide-show<?php
	if ($settings["extra_class"]) echo " " . $settings["extra_class"];

	if ($slide_show->is_fixed_height()) {
		echo " fixed-height";
	} else if ($slide_show->is_fullscreen()) {
		echo " fullscreen";
	}
	?>"
	data-slideshow="<?php echo $slide_show->id ?>"
	data-timer="<?php echo ($slide_show->timer) ? $slide_show->timer : 10; ?>"
	data-transition-time="300<?php /*echo $slideshow->meta->transition_time */ ?>"
	data-easing="<?php /*echo $slideshow->easing */ ?>"
	>
	<ul class="slides-container" style="width: <?php echo sizeof($slides) * 200; ?>%; height:100%;">
		<?php foreach ($slides as $slide) { ?>

			<?php if ($slide->is_youtube()) : ?>

				<li id="slide-<?php echo $slide->id ?>" data-type="youtube" class="slide youtube-slide slide-type-<?php echo $slide->slide_type; ?>"
					data-width="<?php echo $slide->width; ?>"
					data-height="<?php echo $slide->height; ?>"
					data-youtube-id="<?php echo $slide->video_id; ?>"
					data-autoplay="<?php echo $slide->autoplay; ?>"
					data-keep-proportions="<?php echo $slide->keep_proportions; ?>">
					<div class="video-container"></div>
					<div class="test-container"></div>
				</li>


			<?php elseif ($slide->is_vimeo()) : ?>

				<li id="slide-<?php echo $slide->id ?>" data-type="vimeo" class="slide vimeo-slide slide-type-<?php echo $slide->slide_type; ?>"
					data-width="<?php echo $slide->width; ?>"
					data-height="<?php echo $slide->height; ?>"
					data-video-id="<?php echo $slide->video_id; ?>"
					data-autoplay="<?php echo $slide->autoplay; ?>"
					data-keep-proportions="<?php echo $slide->keep_proportions; ?>">
					<div class="video-container"></div>
					<div class="test-container"></div>
				</li>

			<?php else : ?>

				<li id="slide-<?php echo $slide->id ?>" class="slide slide-type-<?php echo $slide->slide_type; ?>"
					data-width="<?php echo $slide->width; ?>"
					data-height="<?php echo $slide->height; ?>"
					data-src-lg="<?php
						$lg_url = $slide->get_image_url("lg");
						if (file_exists($lg_url)) {
							echo $lg_url;
						} else {
							echo $slide->get_image_url();
						};
					?>"
					data-src-md="<?php echo $slide->get_image_url("md"); ?>"
					data-src-sm="<?php echo $slide->get_image_url("sm"); ?>"
					>

					<?php if ($slide->link) : ?>
						<a href="<?php echo $slide->link ?>">
					<?php endif; ?>

					<?php if ($slide->title) : ?>
						<div class="title">
							<span><?php echo $slide->title ?></span>
						</div>
					<?php endif; ?>

					<hr class="main-divider">

					<?php if ($slide->text1) : ?>
						<div class="text1">
							<span><?php echo $slide->text1 ?></span>
						</div>
					<?php endif; ?>
					<?php if ($slide->text2) : ?>
						<div class="text2">
							<span><?php echo $slide->text2 ?></span>
						</div>
					<?php endif; ?>
					<?php if ($slide->text3) : ?>
						<div class="text3">
							<span><?php echo $slide->text3 ?></span>
						</div>
					<?php endif; ?>

					<div class="image" <?php if ($slide->vertical_align) : ?>style="background-position-y: <?php echo $slide->vertical_align; ?> !important;"<?php endif; ?>></div>

					<?php if ($slide->link) : ?>
						</a>
					<?php endif; ?>
				</li>
				<?php endif; ?>
		<?php } ?>
	</ul>

	<?php if (sizeof($slides) > 1) : ?>
		<a href="#" class="prev-link icon-link"><i class="prev-icon icon"></i></a>
		<a href="#" class="next-link icon-link"><i class="next-icon icon"></i></a>

		<!-- Dots -->
		<ul class="imsl-dots">
			<?php
			$first = true;
			$position = 0;
			foreach ($slides as $slide){ ?>
				<li id="imsl-dot-<?php echo $slide->id ?>"
					data-slide-position="<?php echo $position++ ?>"
					<?php if ($first){
						echo ' class="active"';
						$first = false;
					} ?>
				></li>
			<?php } ?>
		</ul>
	<?php endif; ?>
</div>
