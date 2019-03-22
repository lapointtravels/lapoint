
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
	

	<script src="https://snippets.freshchat.com/js/freshchat-business-hours.js"></script>
	<!-- All the below time stamps are in GMT. This is done in order to have the script work across all regions -->
	<script>
	  var business_hours_config = {
	    "Sunday": {
	      from: '02:00 AM',
	      to: '02:01 AM'
	    },
	    "Monday": {
	      from: '09:00 AM',
	      to: '3:00 PM'
	    },
	    "Tuesday": {
	      from: '09:00 AM',
	      to: '3:00 PM'
	    },
	    "Wednesday": {
	      from: '09:00 AM',
	      to: '3:00 PM'
	    },
	    "Thursday": {
	      from: '09:00 AM',
	      to: '3:00 PM'
	    },
	    "Friday": {
	      from: '09:00 AM',
	      to: '3:00 PM'
	    },
	    "Saturday": {
	      from: '02:00 AM',
	      to: '02:01 AM'
	    }
	  };
	</script>
	<script>
	  window.fcSettings = {
	    token: "641c4bc2-4cc6-4be2-b593-544726b9598e",
	    host: "https://wchat.freshchat.com",
	    config: {
	      cssNames: {
	        //The below element is mandatory. Please do not modify this.
	        widget: 'custom_fc_frame',
	        //The below element is mandatory. Please do not modify this
	        expanded: 'custom_fc_expanded'
	      }
	    },
	    onInit: function() {
	      fcBusinessHours.initBusinessHours(business_hours_config);
	    }
	  };
	</script>

	<script src="https://wchat.freshchat.com/js/widget.js" async></script> 

	<!--
	<script>
	  window.fcWidget.init({
	    token: "641c4bc2-4cc6-4be2-b593-544726b9598e",
	    host: "https://wchat.freshchat.com"
	  });
	</script>
	-->
</body>
</html>