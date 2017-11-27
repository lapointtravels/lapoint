<?php
global $kloon_slides;
$settings = $kloon_slides->get_settings();
$image_presentations = $settings["image_presentations"];
$video_presentations = $settings["video_presentations"];
?>

<script type="text/template" id="presentation-edit-fields-template">
	<% if (presentation) { %>
		<% _.each(presentation.fields, function (field) { %>
			<% if (field.type == 'textfield') { %>
				<li>
					<label><%= field.label %>:</label>
					<input type="text" value="<%= slide.presentation_data[field.key] %>" class="presentation-<%= field.key %>">
				</li>
			<% } else if (field.type == 'select') { %>
				<li>
					<label><%= field.label %>:</label>
					<select class="presentation-<%= field.key %>">
						<% _.each(field.options, function (value, key) {  %>
							<option value="<%= key %>" <% if (slide.presentation_data[field.key] == key) print(' selected="selected"') %>><%= value %></option>
						<% }) %>
					</select>
				</li>
			<% } else if (field.type == 'link') { %>
				<li>
					<label><%= field.label %> label:</label>
					<input type="text" value="<%= slide.presentation_data[field.key + '_label'] %>" class="presentation-<%= field.key %>-label">
				</li>
				<li>
					<label><%= field.label %> url:</label>
					<input type="text" value="<%= slide.presentation_data[field.key + '_url'] %>" class="presentation-<%= field.key %>-url">
				</li>
			<% } %>
		<% }) %>
	<% } %>
</script>

<script type="text/template" id="presentation-fields-template">
	<% if (presentation) { %>
		<% _.each(presentation.fields, function (field) { %>
			<% if (field.type == 'textfield') { %>
				<li>
					<span><%= field.label %>:</span>
					<span><%= slide.presentation_data[field.key] %></span>
				</li>
			<% } else if (field.type == 'select') { %>
				<li>
					<span><%= field.label %>:</span>
					<span><%= field.options[slide.presentation_data[field.key]] || '-' %></span>
				</li>
			<% } else if (field.type == 'link') { %>
				<li>
					<span><%= field.label %> label:</span>
					<span><%= slide.presentation_data[field.key + '_label'] %></span>
				</li>
				<li>
					<span><%= field.label %> url:</span>
					<span><%= slide.presentation_data[field.key + '_url'] %></span>
				</li>
			<% } %>
		<% }) %>
	<% } %>
</script>

<script type="text/template" id="video-presentation-fields-template">
	<%
	var slideData = slide.data || {};
	%>

	<% if (presentation && presentation.settings && presentation.settings.has_bgr_video) { %>
		<li class="li-bgr-video-ogv li-bgr-video">
			<% if (slideData.background_video_ogv) { %>
				<span><%= slideData.background_video_ogv.filename %></span>
				<div class="plm">
					<a href="#" class="remove-ogv-video"><?php _e("Remove video", "kloonslides"); ?></a>
				</div>
			<% } else { %>
				<label for="slide-<%= slide.id %>-bgr-video-ogv">Bgr video (ogv):</label>
				<a href="#" class="select-ogv-video"><?php _e("Select video", "kloonslides"); ?></a>
			<% } %>
		</li>
		<li class="li-bgr-video-mp4 li-bgr-video">
			<% if (slideData.background_video_mp4) { %>
				<span><%= slideData.background_video_mp4.filename %></span>
				<div class="plm">
					<a href="#" class="remove-mp4-video"><?php _e("Remove video", "kloonslides"); ?></a>
				</div>
			<% } else { %>
				<label for="slide-<%= slide.id %>-bgr-video-mp4">Bgr video (mp4):</label>
				<a href="#" class="select-mp4-video"><?php _e("Select video", "kloonslides"); ?></a>
			<% } %>
		</li>
	<% } %>
</script>


<?php // ****************************** Image slide ****************************** ?>
<script type="text/template" id="image-slide-template">
	<%
	var slideData = slide.data || {};
	%>

	<span class="dashicons dashicons-sort sort-handle ball"></span>
	<a href="#" class="dashicons dashicons-admin-generic ball btn-edit"></a>

	<% if (slideData && slideData.image_data) { %>
		<img src="<%= slideData.image_data.thumbnail %>" class="preview" />
	<% } %>

	<div class="slide-info">
		<ul class="info-list">
			<li>
				<span>Type:</span>
				<span><%= (presentation) ? presentation.label : '-' %></span>
			</li>
			<%= presentationOutput %>
		</ul>
	</div>
</script>

<script type="text/template" id="image-slide-edit-template">
	<%
	var slideData = slide.data || {};
	%>
	<span class="dashicons dashicons-sort sort-handle ball"></span>

	<% if (slideData.image_data) { %>
		<img src="<%= slideData.image_data.thumbnail %>" class="preview" />
	<% } %>

	<div class="slide-info">
		<ul class="edit-info-list" data-presentation="<%= slideData.presentation %>">
			<li>
				<label for="slide-<%= slide.id %>-presentation">Type:</label>
				<select id="slide-<%= slide.id %>-presentation" class="slide-presentation-select">
					<?php foreach ($image_presentations as $image_presentation): ?>
						<option value="<?php echo $image_presentation["id"]; ?>" <% if (slideData.presentation == <?php echo $image_presentation["id"]; ?>) print('selected="selected"'); %>>
							<?php echo $image_presentation["label"]; ?>
						</option>
					<?php endforeach; ?>
				</select>
			</li>
		</ul>

		<ul class="presentation-fields edit-info-list"></ul>

		<div class="btn-container">
			<a href="#" class="btn-update-slide button-primary">Update</a>
			<a href="#" class="btn-edit button-secondary">Cancel</a>
			<a href="#" class="btn-delete-slide button-primary button-delete">Delete slide</a>
		</div>
	</div>
</script>


<?php // ****************************** Video slide ****************************** ?>
<script type="text/template" id="video-slide-template">
	<%
	var slideData = slide.data || {};
	%>

	<span class="dashicons dashicons-sort sort-handle ball"></span>
	<a href="#" class="dashicons dashicons-admin-generic ball btn-edit"></a>

	<div class="youtube preview">
		Video
	</div>

	<div class="slide-info">
		<ul class="info-list">
			<li>
				<span>Video type:</span>
				<span><%= slideData.video_type %></span>
			</li>
			<li>
				<span>Video ID:</span>
				<span><%= slideData.video_id %></span>
			</li>
			<li>
				<span>Width:</span>
				<span><%= slideData.width %></span>
			</li>
			<li>
				<span>Height</span>
				<span><%= slideData.height %></span>
			</li>
			<li>
				<span>Autoplay:</span>
				<span><%= slideData.autoplay %></span>
			</li>
			<li>
				<span>Keep proportions</span>
				<span><%= slideData.keep_proportions %></span>
			</li>

			<%= presentationOutput %>

			<% if (presentation && presentation.settings && presentation.settings.has_bgr_video) { %>
				<li>
					<span>Bgr video (ogv):</span>
					<span>
						<% if (slideData.background_video_ogv) { %>
							<%= slideData.background_video_ogv.filename %>
						<% } else { %>
							-
						<% } %>
					</span>
				</li>
				<li>
					<span>Bgr video (mp4):</span>
					<span>
						<% if (slideData.background_video_mp4) { %>
							<%= slideData.background_video_mp4.filename %>
						<% } else { %>
							-
						<% } %>
					</span>
				</li>
			<% } %>
		</ul>
	</div>

</script>

<script type="text/template" id="video-slide-edit-template">
	<%
	var slideData = slide.data || {};
	%>

	<span class="dashicons dashicons-sort sort-handle ball"></span>

	<div class="youtube preview">
		Video
	</div>

	<div class="slide-info">
		<ul class="edit-info-list lbl-md" data-video-type="<%= slideData.video_type %>">
			<li>
				<label for="slide-<%= slide.id %>-video-type">Video type:</label>
				<select id="slide-<%= slide.id %>-video-type" class="video-type">
					<option value="youtube" <% if (slideData.video_type == 'youtube') print(' selected="selected"'); %>>Youtube</option>
					<option value="vimeo" <% if (slideData.video_type == 'vimeo') print(' selected="selected"'); %>>Vimeo</option>
				</select>
			</li>
			<li>
				<label for="slide-<%= slide.id %>-video-id">Video ID:</label>
				<input id="slide-<%= slide.id %>-video-id" class="video-id" type="text" value="<%= slideData.video_id %>">
			</li>
			<li>
				<label for="slide-<%= slideData.id %>-width">Width:</label>
				<input id="slide-<%= slide.id %>-width" class="slide-width" type="text" value="<%= slideData.width %>">
			</li>
			<li>
				<label for="slide-<%= slide.id %>-height">Height:</label>
				<input id="slide-<%= slide.id %>-height" class="slide-height" type="text" value="<%= slideData.height %>">
			</li>
			<li>
				<label for="slide-<%= slide.id %>-autoplay">Autoplay:</label>
				<input id="slide-<%= slide.id %>-autoplay" class="video-autoplay" type="checkbox" <% if (slideData.autoplay == "1") print('checked="checked"'); %>>
			</li>
			<li>
				<label for="slide-<%= slide.id %>-keep-proportions">Keep proportions:</label>
				<input id="slide-<%= slide.id %>-keep-proportions" class="video-keep-proportions" type="checkbox" <% if (slideData.keep_proportions == "1") print('checked="checked"'); %>>
			</li>

			<li>
				<label for="slide-<%= slide.id %>-presentation">Type:</label>
				<select id="slide-<%= slide.id %>-presentation" class="slide-presentation-select">
					<?php foreach ($video_presentations as $video_presentation): ?>
						<option value="<?php echo $video_presentation["id"]; ?>" <% if (slideData.presentation == <?php echo $video_presentation["id"]; ?>) print('selected="selected"'); %>>
							<?php echo $video_presentation["label"]; ?>
						</option>
					<?php endforeach; ?>
				</select>
			</li>
		</ul>

		<ul class="presentation-fields edit-info-list lbl-md"></ul>
		<ul class="custom-presentation-fields edit-info-list lbl-md"></ul>

		<div class="btn-container">
			<a href="#" class="btn-update-slide button-primary">Update</a>
			<a href="#" class="btn-edit button-secondary">Cancel</a>
			<a href="#" class="btn-delete-slide button-primary button-delete">Delete slide</a>
		</div>
	</div>
</script>

	<section id="slides-section">
		<ul id="slides-container">
		</ul>
	</section>
