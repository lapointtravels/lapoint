<!-- Module Box -->
<script type="text/template" id="kmc-content-component-inner-template">
	<div class="body">
		<% if (edit) { %>
			Loading...
		<% } else { %>
			<div class="kmc-preview">
				<% if (post.post_content) { %>
					<%= post.post_content %>
				<% } else { %>
					<p class="missing-content">No content added</p>
				<% } %>
			</div>
		<% } %>
	</div>
</script>

<script type="text/template" id="kmc-content-component-template">
	<div class="kmc-content-module">
		<header class="header"></header>
		<div class="content-container"></div>
		<div class="edit-container">

			<ul class="form-group form-list lbl-xxs field-lg shared-only">
				<li>
					<label for="post-title">Title:</label>
					<input type="text" class="post-title form-control" value="<%= post.post_title %>" placeholder="Title" style="width: 300px; max-width: 90%;">
					 <span class="admin-title-label">(only visible in admin)</span>
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


<!-- Theme Module Box -->
<script type="text/template" id="kmc-theme-component-template">
	<div class="kmc-theme-component">
		<header class="header"></header>
		<div class="body">
			<% if (edit) { %>

				<% if (fields.length === 0) {  %>

					<p class="center">Den här komponenten har inga inställningar</p>

				<% } else { %>

					<ul class="form-group form-list lbl-lg field-md">
						<% _.each(fields, function (field) { %>
							<li>
								<label for="<%= field.key %>"><%= field.label %>:</label>
								<% if (field.type === 'textfield') { %>
									<input type="text" class="form-control <%= field.key %>" value="<%= all[field.key] %>" placeholder="<%= field.placeholder || '' %>" data-update="<%= field.key %>">
								<% } else if (field.type === 'textarea') { %>
									<textarea class="form-control <%= field.key %>" placeholder="<%= field.placeholder || '' %>" data-update="<%= field.key %>"><%= all[field.key] %></textarea>
								<% } else if (field.type === 'image') { %>
									<span data-key="<%= field.key %>" class="<%= field.key %> image-field image-select-row"></span>
								<% } else if (field.type === 'select') { 
									var options = [];
									_.each(field.options, function(value, key) {
										options.push([key, value]);
									});
									%>
									<select class="form-control" data-update="<%= field.key %>">
										<%= kmc.helpers.render_options(options, all[field.key]) %>
									</select>
								<% } %>
							</li>
						<% }) %>
					</ul>

				<% } %>

			<% } else { %>

				<div class="kmc-preview pal center">
					<%= admin_presentation %>
				</div>

			<% } %>

		</div>
	</div>
</script>

<script type="text/template" id="kmc-tc-image-row-template">
	<% if (image) { %>
		<img src="<%= image.thumbnail %>" style="width: 80px; height: 80px; float: left;">
		<div class="pam pull-left">
			<a href="#" class="remove-image">Remove image</a>
		</div>
	<% } else { %>
		<a href="#" class="select-image">Select image</a>
	<% } %>
</script>
