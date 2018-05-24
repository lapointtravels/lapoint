
	</main> <!-- #main -->

	<footer class="main-footer">
		<div class="container">
			<div class="row">
				<div class="widget-wrapper col-sm-8 col-md-8">
					<?php dynamic_sidebar('footer'); ?>
				</div>
				<div class="widget footer-right-col col-sm-4 col-md-4">
					<?php dynamic_sidebar('footer-right-col'); ?>
				</div>
			</div>
		</div>
	</footer>

	<script type="text/javascript">
	var theme_url = '<?php echo THEME_URL; ?>';
	var lapoint = {
		language: '<?php echo ICL_LANGUAGE_CODE; ?>',
		country: '<?php echo wpml_get_code(ICL_LANGUAGE_CODE); ?>',
		travelize_wrapper: '<?php echo site_url(); ?>/travelize-wrapper.php',
		loc: {
			book: '<?php _e('Book', 'lapoint'); ?>'
		},
		destination_type: <?php
			global $DESTINATION_TYPE;
			if ($DESTINATION_TYPE) :
				echo "{ link: '" . $DESTINATION_TYPE->link . "' } ";
			else :
				echo "false";
			endif;
		?>,
		gaClientId: (typeof ga !== 'undefined' && typeof ga.getAll === 'function') ? ga.getAll()[0].get('clientId') : false
	};

	if (jQuery("select").length) {
		jQuery('select').select2({
			minimumResultsForSearch: Infinity
		});
	}

	setTimeout(function () {
		jQuery(".animate-directly").addClass("in-view");
	}, 50);

	function setGaClient () {
		if (typeof ga !== 'undefined' && typeof ga.getAll === 'function') {
			lapoint.gaClientId = ga.getAll()[0].get('clientId');
		} else {
			setTimeout(setGaClient, 1000);
		}
	}
	setGaClient();
	var require = {urlArgs: 'bust=18'};
	</script>
	<?php wp_footer(); ?>
	<script data-main="<?php echo THEME_URL; ?>/js/app" src="<?php echo THEME_URL; ?>/js/vendor/require.js"></script>

</body>
</html>