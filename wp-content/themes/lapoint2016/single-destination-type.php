<?php
/**
 * @package WordPress
 * @subpackage Lapoint2016
 * @since Lapoint2016 1.0
 */

global $updated_wp_query;

get_header(); ?>

	<?php lapoint_header_image(); ?>

	<div class="components-container">

		<?php
		if ($updated_wp_query) :

			while ($updated_wp_query->have_posts()) : $updated_wp_query->the_post();
				global $module_controller;
				$module_controller->render_page($post->ID);

			endwhile;

		else :

			while (have_posts()) : the_post();
				if (!get_page_template_slug()) :

					global $module_controller;
					$module_controller->render_page($post->ID);

				else :

					the_content();
				endif;

			endwhile;

		endif;
		?>

	</div>
	
<?php get_footer(); ?>
