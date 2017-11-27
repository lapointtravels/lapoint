<!-- Imageslider Module Box -->
<script type="text/template" id="kmc-imageslider-component-template">
	<div class="kmc-imageslider-component">
		<header class="header"></header>
		<div class="body">
			<% if (edit) { %>

				<p>
					This slide show can only be edit from the Slide show section. <a href="<?php echo admin_url('admin.php'); ?>?page=imageslider-edit-slide-show&slide_show_id=<%= post.ID %>">Edit slide show</a>
				</p>

			<% } else { %>

				<div class="slides">
					<%
					var show_slides = _.reject(slides, function(slide) { return !slide.url; });
					if (show_slides.length > 4) {
						show_slides = show_slides.slice(0, 4);
					}
					var slide_count = show_slides.length;
					var slide_percent = 100 / slide_count;
					_.each(show_slides, function(slide) { %>
						<div style="width: <%= slide_percent %>%; background-image: url('<%= slide.image_url %>');" class="kmc-imageslider-slide">
						</div>
					<% }) %>
				</div>

			<% } %>
		</div>
	</div>
</script>