<?php
/**
 * @package WordPress
 * @subpackage Lapoint2016
 * @since Lapoint2016 1.0
 */

get_header(); ?>

	<div style="background-color: #888; height: 250px;"></div>

	<div class="container row mvl">
		<div class="col-sm-8">
			<?php if ( have_posts() ) : the_post(); ?>

				<?php get_template_part('template-parts/content', 'single'); ?>

			<?php endif; ?>
		</div>

		<?php if (is_active_sidebar('posts-sidebar')) : ?>
			<div class="col-sm-4">
				<aside id="secondary" class="sidebar widget-area" role="complementary">
					<?php dynamic_sidebar('posts-sidebar'); ?>
				</aside>
			</div>
		<?php endif; ?>

	</div>


	<?php // get_sidebar( 'content-bottom' ); ?>

<?php get_footer(); ?>
