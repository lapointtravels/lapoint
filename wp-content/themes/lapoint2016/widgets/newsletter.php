
	<div class="newsletter collecta-form" style="display:none;">

		<?php
		if (class_exists('WPCollecta')) :
			global $wpCollecta;
			$wpCollecta->setupForm();
		endif;
		?>

		<input type="hidden" id="collecta-lang" value="<?php echo ICL_LANGUAGE_CODE; ?>">

		<div id="collecta-container">
			<input type="text" id="collecta-email" class="collecta-field" value="" data-placeholder="<?php echo get_option('collecta-email-placeholder-'. ICL_LANGUAGE_CODE); ?>">
			<a href="#" id="collecta-submit" class="btn btn-inverted"><?php _e('Go', 'collecta'); ?></a>
		</div>

		<div id="collecta-thanks" style="display: none;">
            <span><?php echo get_option('collecta-thanks-'. ICL_LANGUAGE_CODE); ?></span>
        </div>

	</div>