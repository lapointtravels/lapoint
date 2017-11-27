<!-- Booking Bar Module Box -->
<script type="text/template" id="kmc-intro-section-component-template">
	<div class="kmc-intro-section-component">
		<header class="header"></header>
		<div class="body">
			<% if (edit) { %>

				<ul class="form-group form-list lbl-lg field-lg">
					<li>
						<label for="background-color">Title:</label>
						<input type="text" class="form-control post-title" value="<%= post.post_title %>" placeholder="Title">
					</li>
					<li>
						<label for="post-content">Ingress:</label>
						<textarea id="post-content" class="form-control post-content"><%= post.post_content %></textarea>
					</li>
					<li>
						<label for="meta-content1-title">Column 1 title:</label>
						<input id="meta-content1-title" type="input" data-update="col1_title" class="form-control" value="<%= col1_title %>">
					</li>
					<li>
						<label for="meta-content1-content">Column 1 content:</label>
						<textarea id="meta-content1-content" data-update="col1_content" class="form-control"><%= col1_content %></textarea>
					</li>
					<li>
						<label for="meta-content2-title">Column 2 title:</label>
						<input id="meta-content2-title" type="input" data-update="col2_title" class="form-control" value="<%= col2_title %>">
					</li>
					<li>
						<label for="meta-content2-content">Column 2 content:</label>
						<textarea id="meta-content2-content" data-update="col2_content" class="form-control"><%= col2_content %></textarea>
					</li>
				</ul>

			<% } else { %>

				<div class="intro-section-element">

					<% if (post.post_title) { %>
						<h1><%= post.post_title %></h1>
					<% } %>
					<% if (post.post_content) { %>
						<div class="ingress">
							<p><%= post.post_content %></p>
						</div>
					<% } %>

					<div class="content-row row">
						<div class="col-sm-6">
							<div class="inner">
								<% if (col1_title) { %>
									<h2><%= col1_title %></h2>
								<% } %>
								<% if (col1_content) { %>
									<p><%= col1_content %></p>
								<% } %>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="inner">
								<% if (col2_title) { %>
									<h2><%= col2_title %></h2>
								<% } %>
								<% if (col2_content) { %>
									<p><%= col2_content %></p>
								<% } %>
							</div>
						</div>
					</div>

				</div>

			<% } %>
		</div>
	</div>
</script>