<?php
global $kloon_slides;
$slideshows = $kloon_slides->get_all_slideshows();
?>

<!-- Slideshow Module Box -->
<script type="text/template" id="kmc-kmcslides-component-template">
	<div class="kmc-kmcslides-component">
		<header class="header"></header>
		<div class="body">
			<% if (edit) { %>

				<ul class="form-group form-list lbl-lg field-md shared-only">
					<li>
						<label for="post-title">Admin title:</label>
						<input type="text" class="form-control post-title" value="<%= post.post_title %>" placeholder="Title">
						<span class="admin-title-label">(only visible in admin)</span>
					</li>
				</ul>

				<ul class="form-group form-list lbl-lg field-md">
					<li>
						<label for="slideshow_id">Slideshow:</label>
						<select id="slideshow_id" class="form-control content-type" data-update="slideshow_id">
							<%= kmc.helpers.render_options([
								['', '--- Select slideshow ---']
								<?php foreach ($slideshows as $slideshow) : ?>
									, ['<?php echo $slideshow->id; ?>', '<?php echo $slideshow->title; ?>']
								<?php endforeach ?>
  							], slideshow_id) %>
						</select>
					</li>
				</ul>
				<p>
					You can create new, or edit existing, slideshows in the "Slides" section found in the menu.
				</p>

			<% } else { %>

				<div class="slides kmc-preview">
					<% if (slideshow) { %>
						<span class="slides-label">Slideshow with <%= slideshow._slides.length %> slides</span>
						<div class="slides-wrapper">
							<%
							_.each(slideshow._slides.slice(0, 8), function (slide) { %>
								<% if (slide.is_image) { %>
									<div style="background-image: url('<%= slide.data.image_data.thumbnail %>');" class="kmc-kloon-slide"></div>
								<% } else { %>
									<div class="kmc-kloon-slide">Video</div>
								<% } %>
							<% }) %>
						</div>
					<% } else if (error) { %>
						<span class="error"><%= error %></span>
					<% } else { %>
						<span>Loading preview...</span>
					<% } %>
				</div>

			<% } %>
		</div>
	</div>
</script>