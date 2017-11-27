<!-- Booking Bar Module Box -->
<script type="text/template" id="kmc-quote-section-component-template">
	<div class="kmc-quote-section-component">
		<header class="header"></header>
		<div class="body">
			<% if (edit) { %>

				<ul class="form-group form-list lbl-sm field-lg">
					<li>
						<label for="background-color">Quote:</label>
						<input type="text" class="form-control post-title" value="<%= post.post_title %>" placeholder="Title">
					</li>
					<li>
						<label for="quote-name">Name:</label>
						<input type="text" class="form-control quote-name" data-update="name" value="<%= name %>" placeholder="Name">
					</li>
					<li>
						<label class="mrm">Background image:</label>
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

				<div class="kmc-preview">
					<div class="center quote-image" <%
					if (image) { %>
						style="background-image:url('<%= image.sizes.full.url %>');"
						<%
					}
					%>>
						<div class="inner">
							<% if (post.post_title) { %>
								<p class="quote"><%= post.post_title %></p>
							<% } %>
							<% if (name) { %>
								<p class="quote-name"><%= name %></p>
							<% } %>
						</div>
					</div>
				</div>

			<% } %>
		</div>
	</div>
</script>