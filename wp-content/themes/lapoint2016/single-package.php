<?php
/**
 * @package WordPress
 * @subpackage Lapoint2016
 * @since Lapoint2016 1.0
 */

get_header(); ?>

	<?php lapoint_header_image(); ?>


	<div class="components-container">

		<?php
		while (have_posts()) : the_post();

			global $module_controller;
			$module_controller->render_page($post->ID);

		endwhile;
		?>

	</div>

	<script>
		if( window.fbq ) {
			fbq('track','ViewContent');
		} else {
			setTimeout(function(){
				if( window.fbq ) {
					fbq('track','ViewContent');				
				}
			}, 1000)
		}
	</script>

<?php get_footer(); ?>
