<?php
/**
 * Template Name: Page composer
 *
 * @package Wordpress
 * @subpackage Lapoint2016
 * @since Lapoint2016 1.0
*/

get_header();

	lapoint_header_image(); ?>

	<div class="components-container">

		<?php
		while ( have_posts() ) : the_post();
			global $post, $module_controller;
			$module_controller->render_page($post->ID, is_preview());
		endwhile;
		?>

	</div>


<?php get_footer(); ?>