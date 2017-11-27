<?php
global $kloon_slides;
$settings = $kloon_slides->get_settings();
?>

<script>
window.KloonSlides = window.KloonSlides || {};
window.KloonSlides.settings = <?php echo json_encode($settings); ?>;
// window.KloonSlides.protocol = '<?php echo stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://'; ?>';
</script>

<script type="text/template" id="kloonslides-modal-video-template">

	<div class="video-overlay"></div>
	<div class="video-modal">
		<i class="close icon-close"></i>
		<iframe width="100%" height="100%" src="https://www.youtube.com/embed/<%= url %>?autoplay=<%= autoplay %>" frameborder="0" allowfullscreen style="display: none;"></iframe>
	</div>

</script>