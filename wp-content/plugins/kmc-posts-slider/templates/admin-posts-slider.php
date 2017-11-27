<!-- Booking Bar Module Box -->
<script type="text/template" id="kmc-posts-slider-component-template">
	<div class="kmc-posts-slider-component kmc-component">
		<header class="header"></header>
		<div class="body">
			<% if (edit) { %>

			<ul class="form-group form-list lbl-sm field-lg">
				<li>
					<label for="background-color">Title:</label>
					<input type="text" class="form-control post-title" value="<%= post.post_title %>" placeholder="Title">
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
					This area will hold news posts
				</div>

			<% } %>
		</div>
	</div>
</script>