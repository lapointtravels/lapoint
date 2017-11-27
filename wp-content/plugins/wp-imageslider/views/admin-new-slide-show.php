<?php
global $title, $screen_layout_columns;
add_meta_box("imageslider_content", $title, "imageslider_meta_box", "imageslider_new", "normal", "core");
?>

<div class="wrap">

	<div id="imageslider-index-container" class="metabox-holder">
		<?php do_meta_boxes('imageslider_new','normal', null); ?>
	</div>

</div>


<?php
function imageslider_meta_box(){
global $wpdb, $kloon_image_slider;
?>

	<form name="frmImageSlider" action="<?php echo admin_url('admin.php') .'?page=imageslider-new'; ?>" method="post">
		<input type="hidden" name="admin-action" value="create" />

		<ul class="imsl-ul-settings">
			<li>
				<label>Title:</label>
				<input type="text" name="slide_show_title" value="">
			</li>
			<?php //if (!(isset($kloon_image_slider->get_settings()["exclude_setting"]) && in_array("fixed_height", $kloon_image_slider->get_settings()["exclude_setting"]))) : ?>
				<li>
					<label>Storlek:</label>
					<select name="slide_show_size">
						<option value="dynamic">Dynamisk</option>
						<option value="fixed">Fast höjd</option>
						<option value="fullscreen">Helskärm</option>
					</select>
				</li>
				<?php /*
				<li>
					<label>Fast höjd:</label>
					<input type="checkbox" name="slide_show_fixed_height" value="true">
				</li>
				*/ ?>
			<?php //endif; ?>
		</ul>

		<input type="submit" class="button-primary" value="Add slide show" />

	</form>

<?php } ?>