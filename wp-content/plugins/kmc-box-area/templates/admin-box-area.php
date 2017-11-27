<!-- Box Area Module Box -->
<script type="text/template" id="kmc-text-box-dialog-template">
	<div class="md-modal" id="box-dialog">
		<div class="md-content">
			<header>
				<h3>Add Text Box</h3>
				<a href="#" class="md-close"><span class="dashicons dashicons-no"></span></a>
			</header>
			<div class="md-body">
				<ul class="form-group form-list lbl-sm field-md">
					<li>
						<label for="box-title">Title:</label>
						<input id="box-title" type="input" class="form-control box-title" value="<%= box ? box.get("post").post_title : "" %>" placeholder="Title">
					</li>
					<li>
						<label for="box-content">Content:</label>
						<textarea id="box-content" class="form-control box-content"><%= box ? box.get("post").post_content : "" %></textarea>
					</li>
					<li>
						<label for="box-button-text">Button text:</label>
						<input id="box-button-text" type="input" class="form-control box-button-text" value="<%= box ? box.get("button_text") : "" %>">
					</li>
					<li>
						<label for="box-button-link">Button link:</label>
						<input id="box-button-link" type="input" class="form-control box-button-link" value="<%= box ? box.get("button_link") : "" %>">
					</li>
				</ul>

				<button type="button" class="btn-save button button-secondary"><%= box ? "Update" : "Add" %></button>

			</div>
		</div>
	</div>
	<div class="md-overlay"></div>
</script>
<script type="text/template" id="kmc-box-image-row-template">
	<label>Background image:</label>
	<% if (image) { %>
		<img src="<%= image.thumbnail %>" style="width: 80px; height: 80px;">
		<div class="pam">
			<a href="#" class="remove-image">Remove image</a>
		</div>
	<% } else { %>
		<a href="#" class="select-image">Select image</a>
	<% } %>
</script>
<script type="text/template" id="kmc-preview-box-dialog-template">
	<div class="md-modal" id="box-dialog">
		<div class="md-content">
			<header>
				<h3>Add Preview Box</h3>
				<a href="#" class="md-close"><span class="dashicons dashicons-no"></span></a>
			</header>
			<div class="md-body">
				<ul class="form-group form-list field-md">
					<li>
						<label for="box-title">Title:</label>
						<input id="box-title" type="input" class="form-control box-title" value="<%= box ? box.get("post").post_title : "" %>" placeholder="Title">
					</li>
					<li class="image-select-row"></li>
					<li>
						<label for="box-cols-md">Box width, 3 col:</label>
						<select id="box-cols-md" class="form-control box-cols-md">
							<option value="1"<% if (box && box.get("cols_md") == 1) print(" selected='selected'"); %>>1 column</option>
							<option value="2"<% if (box && box.get("cols_md") == 2) print(" selected='selected'"); %>>2 columns</option>
						</select>
					</li>
					<li>
						<label for="box-rows-md">Box height, 3 col:</label>
						<select id="box-rows-md" class="form-control box-rows-md">
							<option value="1"<% if (box && box.get("rows_md") == 1) print(" selected='selected'"); %>>1 row</option>
							<option value="2"<% if (box && box.get("rows_md") == 2) print(" selected='selected'"); %>>2 rows</option>
						</select>
					</li>
					<li>
						<label for="box-cols-sm">Box width, 2 col:</label>
						<select id="box-cols-sm" class="form-control box-cols-sm">
							<option value="1"<% if (box && box.get("cols_sm") == 1) print(" selected='selected'"); %>>1 column</option>
							<option value="2"<% if (box && box.get("cols_sm") == 2) print(" selected='selected'"); %>>2 columns</option>
						</select>
					</li>
					<li>
						<label for="box-rows-sm">Box height, 2 col:</label>
						<select id="box-rows-sm" class="form-control box-rows-sm">
							<option value="1"<% if (box && box.get("rows_sm") == 1) print(" selected='selected'"); %>>1 row</option>
							<option value="2"<% if (box && box.get("rows_sm") == 2) print(" selected='selected'"); %>>2 rows</option>
						</select>
					</li>
					<li>
						<label for="box-button-text">Button text:</label>
						<input id="box-button-text" type="input" class="form-control box-button-text" value="<%= box ? box.get("button_text") : "" %>">
					</li>
					<li>
						<label for="box-button-link">Button link:</label>
						<input id="box-button-link" type="input" class="form-control box-button-link" value="<%= box ? box.get("button_link") : "" %>">
					</li>
				</ul>

				<button type="button" class="btn-save button button-secondary"><%= box ? "Update" : "Add" %></button>

			</div>
		</div>
	</div>
	<div class="md-overlay"></div>
</script>

<script type="text/template" id="kmc-box-area-box-row-template">
	<td style="width: 50px;"><span class="dashicons dashicons-sort sort-handle"></span></td>
	<td><%= label %></td>
	<td><%= post.post_title %></td>
	<td class="right">
		<a href="#" class="remove-link">Remove</a> |
		<a href="#" class="edit-link">Edit</a>
	</td>
</script>
<script type="text/template" id="kmc-box-area-no-rows-template">
	<tr>
		<td colspan="4">There are no boxes added to this area yet.</td>
	</tr>
</script>


<script type="text/template" id="kmc-box-area-component-template">
	<div class="kmc-box-area-component">
		<header class="header"></header>
		<div class="body">
			<% if (edit) { %>

				<ul class="form-group form-list lbl-sm field-md">
					<li>
						<label for="post-title">Admin title:</label>
						<input type="text" class="post-title tabs-title form-control" value="<%= post.post_title %>" placeholder="Title"> <span class="admin-title-label">(only visible in admin)</span>
					</li>
					<li>
						<label for="post-label">Title:</label>
						<input type="text" class="form-control post-label" value="<%= label %>" placeholder="Title" data-update="label">
					</li>
					<li>
						<label for="post-content">Content:</label>
						<textarea id="post-content" class="form-control post-content"><%= post.post_content %></textarea>
					</li>
				</ul>

				<table class="table table-striped mtm mbl">
					<thead>
						<tr>
							<th></th>
							<th style="width: 150px;">Box type</th>
							<th>Title</th>
							<th>&nbsp;</th>
						</tr>
					</thead>
					<tbody class="box-rows-container">
					</tbody>
					<tfoot>
						<td colspan="4">
							<select class="add-box-select">
								<option value="text-box">Text box</option>
								<option value="preview-box">Preview box</option>
							</select>
							<button type="button" class="add-box-btn button button-secondary mls">Add new box</button>
						</td>
					</tfoot>
				</table>


			<% } else { %>


				<% if (label) { %>
					<h2><%= label %></h2>
				<% } %>

				<% if (post.post_content) { %>
					<p class="mas"><%= post.post_content %></p>
				<% } %>

				<div class="row">
					<% _.each(boxes.models, function (box) { %>
						<div class="col-xs-4 box">
							<div class="inner">
								<div class="ptl">
									<span><%= box.label %></span>
									<p><%= box.get("post").post_title %></p>
								</div>
							</div>
						</div>
					<% }) %>
				</div>


			<% } %>
		</div>
	</div>
</script>