<!-- Booking Bar Module Box -->
<script type="text/template" id="kmc-info-box-component-template">
	<div class="kmc-info-box-component">
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
						<label for="top-padding">Icon:</label>
						<select class="form-control" data-update="icon">
							<%= kmc.helpers.render_options([
								["surfer", "Surfer"],
								["surfer2", "Surfer 2"],
  								["kitesurfer", "Kitesurfer"],
  								["longboarder", "Longboarder"],
  								["palmtree", "Palm tree"],
  								["sandals", "Sandals"],
  								["sun", "Sun"]
  							], icon) %>
  						</select>
					</li>
					<li>
						<label for="meta-button-text">Button text:</label>
						<input id="meta-button-text" type="input" data-update="button_text" class="form-control meta-button-text" value="<%= button_text %>">
					</li>
					<li>
						<label for="meta-button-link">Button link:</label>
						<input id="meta-button-link" type="input" data-update="button_link" class="form-control meta-button-link" value="<%= button_link %>">
					</li>
				</ul>

			<% } else { %>

				<div class="info-box-element icon-<%= icon %>">
					<h1><%= post.post_title %></h1>

					<div class="divider"></div>

					<p><%= post.post_content %></p>

					<% if (button_text) { %>
						<a href="<%= button_link %>" class="btn btn-primary"><%= button_text %></a>
					<% } %>
				</div>

			<% } %>
		</div>
	</div>
</script>