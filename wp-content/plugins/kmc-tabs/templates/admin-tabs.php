
<script type="text/template" id="kmc-tab-template">
	<div class="tab-components"></div>
</script>
<script type="text/template" id="kmc-tab-edit-template">
	<div style="position: relative;">
		<ul class="form-group form-list lbl-sm">
			<li>
				<label for="tab-title">Label:</label>
				<input type="text" class="tab-title" value="<%= title %>" placeholder="Title">
			</li>
		</ul>

		<i class="remove-tab"><span class="dashicons dashicons-trash"></span></i>
	</div>
	<div class="tab-components"></div>
	<div class="mvl">
		<button type="button" class="button button-secondary add-tab-component">Add tab content</button>
	</div>
</script>
<script type="text/template" id="kmc-tabs-component-template">
	<div class="kmc-tabs-component">
		<header class="header"></header>
		<div class="tab-body">
			<% if (edit) { %>

				<ul class="form-group form-list lbl-sm">
					<li>
						<label for="post-title">Admin title:</label>
						<input type="text" class="post-title tabs-title form-control" value="<%= post.post_title %>" placeholder="Title"> <span class="admin-title-label">(only visible in admin)</span>
					</li>
					<li>
						<label for="post-label">Title:</label>
						<input type="text" class="form-control post-label" value="<%= label %>" placeholder="Title" data-update="label">
					</li>
				</ul>


				<div class="tabs mbl">
					<ul class="tab-flaps clearfix">
					    <li class="add-btn-container"><i class="add-tab">Add tab</i></li>
					</ul>
				</div>

			<% } else { %>

				<div class="kmc-preview">
					<% if (label) { %>
						<h2><%= label %></h2>
					<% } %>

					<div class="tabs mbl">
						<ul class="clearfix">
						</ul>
					</div>
				</div>

			<% } %>
		</div>
	</div>
</script>