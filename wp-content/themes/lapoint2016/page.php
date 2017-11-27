<?php
/**
 * @package WordPress
 * @subpackage Lapoint2016
 * @since Lapoint2016 1.0
 */

get_header(); ?>


	<?php if (has_post_thumbnail()) : ?>
		<div class="full-image">
			<?php the_post_thumbnail("header-image"); ?>
		</div>
	<?php endif; ?>

	<div class="container mvl">

		<?php while ( have_posts() ) : the_post(); ?>
			<?php the_content(); ?>
		<?php endwhile; ?>

	</div>

<?php get_footer(); ?>
