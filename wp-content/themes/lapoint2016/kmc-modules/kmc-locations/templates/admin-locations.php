
<!-- Location Module Box -->
<script type="text/template" id="kmc-locations-component-template">
	<div class="kmc-locations-component kmc-component">
		<header class="header"></header>
		<div class="body">
			<% if (edit) { %>

				<ul class="form-group form-list lbl-sm field-lg">
					<li>
						<label for="post-title">Title:</label>
						<input type="text" class="form-control post-title" value="<%= post.post_title %>" placeholder="Title">
					</li>
					<li>
						<label for="post-content">Content:</label>
						<textarea class="form-control post-content"><%= post.post_content %></textarea>
					</li>
				</ul>

			<% } else { %>

				<div class="locations-row-preview">
					<% if (post.post_title) { %>
						<h2><%= post.post_title %></h2>
					<% } %>

					<% if (post.post_content) { %>
						<p class="mas"><%= post.post_content %></p>
					<% } %>

					<div class="box-info pal mtm">
						This area will hold Location boxes
					</div>
				</div>

			<% } %>
		</div>
	</div>
</script>

<script type="text/template" id="kmc-locations-preview-template">
	<div class="locations-row-preview pvs">
		<% if (post.post_title) { %>
			<h2><%= post.post_title %></h2>
		<% } %>

		<% if (post.post_content) { %>
			<p class="mas mbm"><%= post.post_content %></p>
		<% } %>

		<div class="box-info pal">
			This area will hold Location boxes
		</div>
	</div>
</script>