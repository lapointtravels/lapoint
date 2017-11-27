<!-- Booking Bar Module Box -->
<script type="text/template" id="kmc-destinations-component-template">
	<div class="kmc-destinations-component kmc-component">
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
					<li>
						<label for="tag">Tag:</label>
						<select class="tag" data-update="tag" class="form-control">
							<option value="h1" <% if (tag == 'h1') print(' selected="selected"') %>>H1</option>
							<option value="h2" <% if (tag != 'h1') print(' selected="selected"') %>>H2</option>
						</select>
					</li>
				</ul>

			<% } else { %>

				<div class="kmc-preview">

					<% if (post.post_title) { %>
						<h2><%= post.post_title %></h2>
					<% } %>

					<% if (post.post_content) { %>
						<p class="mas"><%= post.post_content %></p>
					<% } %>

					<div class="box-info pal mtm">
						This area will hold Destination boxes
					</div>

				</div>

			<% } %>
		</div>
	</div>
</script>