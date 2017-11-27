<!-- Booking Bar Module Box -->
<script type="text/template" id="kmc-alertbar-component-template">
	<div class="kmc-alertbar-component">
		<header class="header"></header>
		<div class="body">
			<% if (edit) { %>

				<ul class="form-group form-list lbl-lg field-md">
					<li>
						<label for="post-title">Admin title:</label>
						<input type="text" class="form-control post-title" value="<%= post.post_title %>" placeholder="Title"> <span class="admin-title-label">(only visible in admin)</span>
					</li>
					<li>
						<label for="meta-button-text">Button text:</label>
						<input id="meta-button-text" type="input" data-update="button_text" class="form-control meta-button-text" value="<%= button_text %>">
					</li>
					<li>
						<label for="meta-button-link">Button link:</label>
						<input id="meta-button-link" type="input" data-update="button_link" class="form-control meta-button-link" value="<%= button_link %>">
					</li>
				</ul>

			<% } else { %>

				<div class="alertbar-element">
					<a href="<%= button_link %>" class="btn-alert btn"><%= button_text %></a>
				</div>

			<% } %>
		</div>
	</div>
</script>