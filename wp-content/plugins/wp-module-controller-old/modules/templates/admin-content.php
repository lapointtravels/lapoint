<!-- Module Box -->
<script type="text/template" id="kmc-content-component-inner-template">
	<div class="body">
		<% if (edit) { %>
			Loading...
		<% } else { %>
			<% if (post.post_content) { %>
				<%= post.post_content %>
			<% } else { %>
				<p class="missing-content">No content added</p>
			<% } %>
		<% } %>
	</div>
</script>

<script type="text/template" id="kmc-content-component-template">
	<div class="kmc-content-module">
		<header class="header"></header>
		<div class="content-container"></div>
		<div class="edit-container">

			<ul class="form-group form-list lbl-xxs field-lg">
				<li>
					<label for="post-title">Title:</label>
					<input type="text" class="post-title form-control" value="<%= post.post_title %>" placeholder="Title"style="width: 300px; max-width: 90%;">
				</li>
			</ul>

			<div class="tinymce-container"></div>

		</div>
	</div>
</script>

<script type="text/template" id="kmc-content-preview-template">
	<div class="pvs">
		<%= post.post_content %>
	</div>
</script>