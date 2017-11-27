<?php
global $destination_types_manager, $destinations_manager, $camps_manager, $levels_manager;
$destination_types = $destination_types_manager->get_all();
$destinations = $destinations_manager->get_all();
$camps = $camps_manager->get_all();
$levels = $levels_manager->get_all();
?>

<script>
var all_destination_types = <?php echo json_encode($destination_types); ?>;
var all_destinations = <?php echo json_encode($destinations); ?>;
var all_camps = <?php echo json_encode($camps); ?>;
var all_levels = <?php echo json_encode($levels); ?>;
</script>

<!-- Booking Bar Module Box -->
<script type="text/template" id="kmc-booking-bar-component-template">
	<div class="kmc-booking-bar-component">
		<header class="header"></header>
		<div class="body">
			<% if (edit) { %>

				<ul class="form-group form-list lbl-lg field-md">
					<li>
						<label for="background-color">Title:</label>
						<input type="text" class="form-control post-title" value="<%= post.post_title %>" placeholder="Title">
					</li>
					<li>
						<label for="default-destination-type">Default destination type:</label>
						<select id="default-destination-type" data-update="default_destination_type" class="form-control">
							<% _.each(all_destination_types, function (destination_type) { %>
								<option value="<%= destination_type.id %>" <%
									if (default_destination_type == destination_type.id) print(' selected="selected"');
								%>><%= destination_type.title %></option>
							<% }) %>
						</select>
					</li>
					<li>
						<label for="default-destination-type">Default destination:</label>
						<select id="default-destination-type" data-update="default_destination" class="form-control">
							<option value="">Default</option>
							<% _.each(all_destinations, function (destination) { %>
								<option value="<%= destination.id %>" <%
									if (default_destination == destination.id) print(' selected="selected"');
								%>><%= destination.title %></option>
							<% }) %>
						</select>
					</li>
					<li>
						<label for="default-camp">Default camp:</label>
						<select id="default-camp" data-update="default_camp" class="form-control">
							<option value="">Default</option>
							<% _.each(all_camps, function (camp) { %>
								<option value="<%= camp.id %>" <%
									if (default_camp == camp.id) print(' selected="selected"');
								%>><%= camp.title %></option>
							<% }) %>
						</select>
					</li>
					<li>
						<label for="default-level">Default level:</label>
						<select id="default-level" data-update="default_level" class="form-control">
							<option value="">Default</option>
							<% _.each(all_levels, function (level) { %>
								<option value="<%= level.id %>" <%
									if (default_level == level.id) print(' selected="selected"');
								%>><%= level.title %></option>
							<% }) %>
						</select>
					</li>
					<li>
						<label for="auto-search">Auto search:</label>
						<select id="auto-search" data-update="auto_search" class="form-control">
							<option value="0" <% if (!auto_search) print(' selected="selected"') %>>No</option>
							<option value="1" <% if (auto_search == 1) print(' selected="selected"') %>>Yes</option>
						</select>
					</li>
				</ul>

			<% } else { %>

				<% if (post.post_title) { %>
					<h2><%= post.post_title %></h2>
				<% } %>

				<p>Travelize Booking Bar</p>

			<% } %>
		</div>
	</div>
</script>