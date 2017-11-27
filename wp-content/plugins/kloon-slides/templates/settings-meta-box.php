<?php
global $kloon_slides, $slideshows_controller;
$slideshow = $slideshows_controller->get_current_slideshow();
?>

<script>
window.kloonslides = window.kloonslides || {};
window.kloonslides.slideshow = <?php echo json_encode($slideshow); ?>;
window.kloonslides.slides = <?php echo json_encode($slideshow->get_slides()); ?>;
window.kloonslides.settings = <?php echo json_encode($kloon_slides->get_settings()); ?>
</script>

	<form id="form-kloonslides-settings" action="#" method="post" onsubmit="return false;">
		<input type="hidden" id="slideshow-id" value="<?php echo $slideshow->id; ?>">
		<ul class="kloonslides-ul-settings lbl-lg">
			<li>
				<label>Size:</label>
				<select id="slideshow-size" name="slide_show_size">
					<?php /*<option value="dynamic"<?php if ($slideshow->size == "dynamic") echo " selected='selected'"; ?>>Dynamic</option> */ ?>
					<option value="fixed"<?php if ($slideshow->size == "fixed") echo " selected='selected'"; ?>>Fixed height</option>
					<?php /*<option value="fullscreen"<?php if ($slideshow->size == "fullscreen") echo " selected='selected'"; ?>>Fullscreen</option> */ ?>
				</select>
			</li>
			<li>
				<label>Fixed height - Desktop:</label>
				<input type="number" class="sm" id="slideshow-fixed-height-px" value="<?php echo $slideshow->fixed_height_px; ?>">
				<span>px</span>
			</li>
			<li>
				<label>Fixed height - Tablet:</label>
				<input type="number" class="sm" id="slideshow-fixed-height-tablet" value="<?php echo $slideshow->fixed_height_tablet; ?>">
				<span>px</span>
			</li>
			<li>
				<label>Fixed height - Phone:</label>
				<input type="number" class="sm" id="slideshow-fixed-height-phone" value="<?php echo $slideshow->fixed_height_phone; ?>">
				<span>px</span>
			</li>
			<li>
				<label>Hide prev/next buttons:</label>
				<input type="checkbox" id="slideshow-hide-nav" value="1" <?php if ($slideshow->hide_nav) echo " checked='checked'"; ?>>
			</li>
			<li class="clearfix">
				<label>Timer:</label>
				<input type="text" class="sm" id="slideshow-timer" value="<?php echo $slideshow->timer; ?>">
				<span>(How many seconds each slide will be displayed)</span>
			</li>
		</ul>

		<div class="clearfix" style="margin-top: 15px;">
			<button class="button-primary btn-save-settings">Update</button>
			<span id="kloonslides-save-message" style="display:none;">Saving, please wait..</span>
		</div>
	</form>