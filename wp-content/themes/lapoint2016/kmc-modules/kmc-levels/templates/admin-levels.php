<!-- Booking Bar Module Box -->
<script type="text/template" id="kmc-levels-component-template">
	<div class="kmc-levels-component kmc-component">
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

			<% } else { %>

				<% if (post.post_title) { %>
					<h2><%= post.post_title %></h2>
				<% } %>

				<% if (post.post_content) { %>
					<p class="mas"><%= post.post_content %></p>
				<% } %>

				<div class="box-info pal mtm">
					This area will hold Level boxes
				</div>

			<% } %>
		</div>
	</div>
</script>