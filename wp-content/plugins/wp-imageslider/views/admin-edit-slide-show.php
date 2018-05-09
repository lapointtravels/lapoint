<?php
global $slide_show, $wpdb;
$slides = $slide_show->get_slides();

add_meta_box("imageslider_content_slides", "Edit slides", "imsl_edit_meta_box", "imageslider_edit", "normal", "core");
add_meta_box("imageslider_content_upload", "Add new image slides", "imsl_upload_meta_box", "imageslider_upload", "normal", "core");
add_meta_box("imageslider_content_video", "Add new video slide", "imsl_video_meta_box", "imageslider_video", "normal", "core");
add_meta_box("imageslider_content_settings", "Settings", "imsl_settings_meta_box", "imageslider_settings", "normal", "core");


// ****************************** Structure ******************************
?>
<script type="text/javascript">
var slide_show_id = <?php echo $slide_show->id; ?>,
	slides_json = <?php echo json_encode($slides); ?>,
	wp_prefix = '<?php echo $wpdb->prefix; ?>';
</script>

<div id="slideshow-page" class="wrap">
	<div class="metabox-holder">
		<?php do_meta_boxes('imageslider_edit','normal', null); ?>
	</div>

	<div class="metabox-holder">
		<?php do_meta_boxes('imageslider_upload','normal', null); ?>
	</div>

	<div class="metabox-holder">
		<?php do_meta_boxes('imageslider_video','normal', null); ?>
	</div>

	<div class="metabox-holder">
		<?php do_meta_boxes('imageslider_settings','normal', null); ?>
	</div>
</div>



<?php
// ****************************** Edit box ******************************
function imsl_edit_meta_box(){
	global $wpdb, $slide_show, $kloon_image_slider;
	?>

	<script>
	var IMAGE_SLIDER_SETTINGS = <?php echo json_encode($kloon_image_slider->get_settings()); ?>;
	</script>

	<!-- Templates -->
	<script type="text/template" id="slides-template">
		<ul id="edit-slides-list">
	    <% var inner_template = _.template(jQuery('#slide-template') .html()) %>
	    <% _.each(slides, function(slide){ %>
	    	<%= inner_template(slide) %>
	    <% }); %>
	    </ul>
	</script>

	<script type="text/template" id="slide-template">
		<li id="slide-<%= id %>" class="imageslider-slide clearfix" data-slide-id="<%= id %>">
			<% if (video_type) { %>

				<div class="youtube-box">
					Youtube video
				</div>
				<ul class="edit-slide-info" data-slide-type="<%= slide_type %>">
					<li>
						<label for="slide-<%= id %>-video-type">Video type:</label>
						<select id="slide-<%= id %>-video-type" class="video-type">
							<option value="youtube" <% if (video_type == 'youtube') print(' selected="selected"'); %>>Youtube</option>
							<option value="vimeo" <% if (video_type == 'vimeo') print(' selected="selected"'); %>>Vimeo</option>
						</select>
					</li>
					<li>
						<label for="slide-<%= id %>-video-id">Video ID:</label>
						<input id="slide-<%= id %>-video-id" class="video-id" type="text" value="<%= video_id %>">
					</li>
					<li>
						<label for="slide-<%= id %>-width">Bredd:</label>
						<input id="slide-<%= id %>-width" class="slide-width" type="text" value="<%= width %>">
					</li>
					<li>
						<label for="slide-<%= id %>-height">Höjd:</label>
						<input id="slide-<%= id %>-height" class="slide-height" type="text" value="<%= height %>">
					</li>
					<li>
						<label for="slide-<%= id %>-autoplay">Autoplay:</label>
						<input id="slide-<%= id %>-autoplay" class="video-autoplay" type="checkbox" <% if (autoplay == "1") print('checked="checked"'); %>>
					</li>
					<li>
						<label for="slide-<%= id %>-keep-proportions">Dynamisk höjd:</label>
						<input id="slide-<%= id %>-keep-proportions" class="video-keep-proportions" type="checkbox" <% if (keep_proportions == "1") print('checked="checked"'); %>>
					</li>
				</ul>
				<p>
					<input type="button" id="update-slide-<%= id %>" class="update-slide-button button-primary" value="Update" />
					<input type="button" id="delete-slide-<%= id %>" class="delete-slide-button button-secondary" value="Delete slide" />
				</p>
				<p id="slide-<%= id %>-updated" class="slide-updated">Slide updated</p>

			<% } else { %>

				<% if (image_data) { %>
					HAS IMAGE DATA
					<img src="<%= image_data.thumbnail.replace(/(^\w+:|^)\/\//, '//').replace(/(^\/\/www.lapoint.staging)/, '//lapoint.staging') %>" />
				<% } else { %>
					NO IMAGE DATA
					<img src="<?php echo $slide_show->get_upload_root_url(); ?><%= filename + '-thumb.' + type %>">
				<% } %>



				<ul class="edit-slide-info" data-slide-type="<%= slide_type %>">
					<li>
						<label for="slide-<%= id %>-text-1">Typ:</label>
						<select id="slide-<%= id %>-type" class="slide-type-select">
							<?php foreach ($kloon_image_slider->get_settings()["slide_types"] as $imsl_slide_type): ?>
								<option value="<?php echo $imsl_slide_type["id"]; ?>" <% if (slide_type == <?php echo $imsl_slide_type["id"]; ?>) print('selected="selected"'); %>>
									<?php echo $imsl_slide_type["label"]; ?>
								</option>
							<?php endforeach ?>
						</select>
					</li>
					<li class="li-link" style="display: none;">
						<label for="slide-<%= id %>-link">Länk:</label>
						<input id="slide-<%= id %>-link" type="text" class="slide-link" value="<%= link %>">
					</li>
					<li class="li-title" style="display: none;">
						<label for="slide-<%= id %>-title">Rubrik:</label>
						<input id="slide-<%= id %>-title" class="slide-title" type="text" value="<%= title %>">
					</li>
					<li class="li-text-1 li-text" style="display: none;">
						<label for="slide-<%= id %>-text-1">Text 1:</label>
						<input id="slide-<%= id %>-text-1" class="slide-text-1" type="text" value="<%= text1 %>">
					</li>
					<li class="li-text-2 li-text" style="display: none;">
						<label for="slide-<%= id %>-text-2">Text 2:</label>
						<input id="slide-<%= id %>-text-2" class="slide-text-2" type="text" value="<%= text2 %>">
					</li>
					<li class="li-text-3 li-text" style="display: none;">
						<label for="slide-<%= id %>-text-3">Text 3:</label>
						<input id="slide-<%= id %>-text-3" class="slide-text-3" type="text" value="<%= text3 %>">
					</li>
					<?php if ($slide_show->meta["fixed_height"][0]) : ?>
						<li class="li-vertical-align">
							<label for="slide-<%= id %>-vertical-align">Vertical align:</label>
							<select id="slide-<%= id %>-vertical-align" class="vertical-align">
								<option value="center" <% if (vertical_align == "center") print("selected='selected'") %>>Center</option>
								<option value="top" <% if (vertical_align == "top") print("selected='selected'") %>>Top</option>
								<option value="bottom <% if (vertical_align == "bottom") print("selected='selected'") %>">Bottom</option>
							</select>
						</li>
					<?php endif; ?>
				</ul>
				<p>
					<input type="button" id="update-slide-<%= id %>" class="update-slide-button button-primary" value="Update" />
					<input type="button" id="delete-slide-<%= id %>" class="delete-slide-button button-secondary" value="Delete slide" />
				</p>
				<p id="slide-<%= id %>-updated" class="slide-updated">Slide updated</p>
			<% } %>
		</li>
	</script>

	<div id="edit-slides-list-container">
		<ul id="edit-slides-list">
		</ul>
	</div>

	<?php
}



// ****************************** Upload box ******************************
function imsl_upload_meta_box(){
	global $wpdb, $slide_show, $kloon_image_slider;
	$settings = $kloon_image_slider->get_settings();
	if ($settings["use_media_library"]) :
		?>

		<a href="#" class="select-image">Select image</a>

		<?php
	else :
		$plugin_url = $settings["plugin_url"];
		$upload_url = $settings["upload_url"];
		?>

		<!-- Plupload -->
		<input type="hidden" id="plupload-flash-url" value="<?php echo $plugin_url; ?>/js/libs/plupload/js/plupload.flash.swf">
		<input type="hidden" id="plupload-upload-path" value="<?php echo $upload_url; ?>">

		<div id="plupload-container">
			<p>Drop images here, or <a id="plupload-browse-button">browse</a></p>
		</div>

		<ul id="plupload-list"></ul>

		<button id="plupload-submit-button" class="button-primary">Start upload</button>

		<?php
	endif;
}


// ****************************** Upload video box ******************************
function imsl_video_meta_box(){
global $wpdb, $slide_show;
?>

	<form id="form-imsl-video" action="#" method="post">
		<ul class="imsl-ul-settings">
			<li class="clearfix">
				<label>Video type:</label>
				<select id="video-type">
					<option value="youtube">Youtube</option>
					<option value="vimeo">Vimeo</option>
				</select>
			</li>
			<li class="clearfix">
				<label>Video ID:</label>
				<input type="text" id="video-id" value="" placeholder="Ange youtube id">
			</li>
			<li class="clearfix">
				<label>Bredd:</label>
				<input type="text" id="video-width" value="640">
			</li>
			<li class="clearfix">
				<label>Höjd:</label>
				<input type="text" id="video-height" value="320">
			</li>
		</ul>

		<div class="clearfix" style="margin-top: 15px;">
			<input type="submit" class="button-primary" id="imsl-update-video-submit" value="Add slide" />
			<span id="imsl-update-video-saving" style="display:none;">Saving, please wait..</span>
		</div>
	</form>

<?php
}



// ****************************** Settings box ******************************
function imsl_settings_meta_box(){
global $wpdb, $slide_show, $kloon_image_slider;
?>

	<form id="form-update-imsl-settings" action="#" method="post">
		<input type="hidden" id="slide-show-id" value="<?php echo $slide_show->id; ?>">
		<ul class="imsl-ul-settings">
			<li>
				<label>Title:</label>
				<input type="text" id="slide-show-title" value="<?php echo $slide_show->title; ?>">
			</li>
			<?php /*
			<li>
				<label>Transition time:</label>
				<input type="text" id="slide-show-transition-time" value="<?php echo $slide_show->meta["transition_time"][0]; ?>">
			</li>
			<li>
				<label>Easing:</label>
				<select id="slide-show-easing" name="slide-show-easing">
					<option value="<?php echo $slide_show->meta["easing"]; ?>" selected="selected"><?php echo $slide_show->meta["easing"]; ?></option>
				</select>
			</li>
			*/ ?>

			<li>
				<label>Storlek:</label>
				<select id="slide-show-size" name="slide_show_size">
					<option value="dynamic"<?php if ($slide_show->size == "dynamic") echo " selected='selected'"; ?>>Dynamisk</option>
					<option value="fixed"<?php if ($slide_show->size == "fixed") echo " selected='selected'"; ?>>Fast höjd</option>
					<option value="fullscreen"<?php if ($slide_show->size == "fullscreen") echo " selected='selected'"; ?>>Helskärm</option>
				</select>
			</li>

			<?php /*if (!(isset($kloon_image_slider->get_settings()["exclude_setting"]) && in_array("fixed_height", $kloon_image_slider->get_settings()["exclude_setting"]))) : ?>
				<li>
					<label>Fast höjd:</label>
					<input type="checkbox" disabled="disabled" <?php if ($slide_show->meta["fixed_height"][0]) echo " checked='checked'"; ?>>
				</li>
			<?php endif;*/ ?>
			<li class="clearfix">
				<label>Timer:</label>
				<input type="text" id="slide-show-timer" value="<?php echo $slide_show->timer; ?>">
				<span>(Hur många sekunder varje slide ska visas)</span>
			</li>
		</ul>

		<div class="clearfix" style="margin-top: 15px;">
			<input type="submit" class="button-primary" id="imsl-update-settings-submit" value="Update" />
			<span id="imsl-update-settings-saving" style="display:none;">Saving, please wait..</span>
		</div>
	</form>


<?php
}
