<!-- Video Slider Module Box -->
<script type="text/template" id="kmc-video-component-row-template">
	<div class="clearfix">
		<span class="pull-left"><%= title %></span>
		<span class="pull-right"><a href="#" class="remove-video">Remove</a></span>
	</div>
</script>
<script type="text/template" id="kmc-videos-component-template">
	<div class="kmc-videos-component kmc-component">
		<header class="header"></header>
		<div class="body">
			<% if (edit) { %>

				<ul class="form-group form-list lbl-sm field-lg">
					<li>
						<label for="post-title">Title:</label>
						<input type="text" class=" form-control post-title" value="<%= post.post_title %>" placeholder="Title">
					</li>
					<li>
						<label for="post-content">Content:</label>
						<textarea class="form-control post-content"><%= post.post_content %></textarea>
					</li>
				</ul>


				<p class="mbs">Added videos:</p>
				<ul class="videos-list"></ul>

				<button type="button" class="button button-secondary add-video">Add video</button>

			<% } else { %>

				<% if (post.post_title) { %>
					<h2 class="center pbn"><%= post.post_title %></h2>
				<% } %>

				<% if (post.post_content) { %>
					<p class="center mas"><%= post.post_content %></p>
				<% } %>

				<div class="box-info pbl pll prl ptn clearfix">
					<% _.each(videos.models, function (video) { %>
						<% if (video.attributes.thumb) { %>
							<img src="<%= video.attributes.thumb[0] %>" width="150" height="100" style="float: left;" class="mas" />
						<% } %>
					<% }) %>
				</div>

			<% } %>
		</div>
	</div>
</script>

<script type="text/template" id="kmc-video-component-modal-template">
	<div class="md-modal" id="video-add-dialog">
		<div class="md-content">
			<header>
				<h3>Add video</h3>
				<a href="#" class="md-close"><span class="dashicons dashicons-no"></span></a>
			</header>
			<div class="md-body content"></div>
		</div>
	</div>
	<div class="md-overlay"></div>
</script>

<script type="text/template" id="kmc-video-component-modal-content-template">
	<% if (loading) { %>
		<p>Loading...</p>
	<% } else if (error) { %>
		<p>An unexpected error occured...</p>
	<% } else { %>
		<ul class="form-group form-list field-md">
			<li>
				<label for="video">Video:</label>
				<select id="video" name="video" class="form-control video-select">
					<% _.each(availableVideos, function (video) { %>
						<option value="<%= video.id %>"><%= video.title %></option>
					<% }) %>
				</select>
			</li>
		</ul>
		<button type="button" class="btn-save button button-secondary">Add</button>
	<% } %>
</script>