<!-- Booking Bar Module Box -->
<script type="text/template" id="kmc-image-section-component-template">
	<div class="kmc-image-section-component">
		<header class="header"></header>
		<div class="body">
			<% if (edit) { %>

				<ul class="form-group form-list lbl-sm field-lg">
					<li>
						<label for="background-color">Title:</label>
						<input type="text" class="form-control post-title" value="<%= post.post_title %>" placeholder="Title">
					</li>
					<li>
						<label for="post-content">Content:</label>
						<textarea id="post-content" class="form-control post-content"><%= post.post_content %></textarea>
					</li>
					<li>
						<label>Background image:</label>
						<% if (typeof(image) !== "undefined" && image) { %>
							<img src="<%= image.sizes.thumbnail.url %>" style="width: 80px; height: 80px;">
							<div class="pam">
								<a href="#" class="remove-image">Remove image</a>
							</div>
						<% } else { %>
							<a href="#" class="select-image">Select image</a>
						<% } %>
					</li>
				</ul>

			<% } else { %>

				<div class="row kmc-preview">
					<div class="col col-xs-6">
						<% if (typeof(image) !== "undefined" && image) { %>
							<div class="image" style="background-image:url('<%= image.url %>');"></div>
						<% } %>
					</div>
					<div class="col col-xs-6">
						<div class="inner">
							<% if (post.post_title) { %>
								<h2><%= post.post_title %></h2>
							<% } %>
							<% if (post.post_content) { %>
								<p><%= post.post_content %></p>
							<% } %>
						</div>
					</div>
				</div>

			<% } %>
		</div>
	</div>
</script>