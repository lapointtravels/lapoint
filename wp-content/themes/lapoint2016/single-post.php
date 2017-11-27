<?php
/**
 * @package WordPress
 * @subpackage Lapoint2016
 * @since Lapoint2016 1.0
 */

get_header(); ?>

	<div class="full-image">
		<img width="2500" height="618" src="https://www.lapointcamps.com/wp-content/uploads/2016/04/Blogbanner-4.jpg" class="attachment-header-image size-header-image wp-post-image" alt="080316_0860" srcset="https://www.lapointcamps.com/wp-content/uploads/2016/04/Blogbanner-4.jpg 300w, https://www.lapointcamps.com/wp-content/uploads/2016/04/7-768x190.jpg 768w, https://www.lapointcamps.com/wp-content/uploads/2016/04/7-1024x253.jpg 1024w, https://www.lapointcamps.com/wp-content/uploads/2016/04/Blogbanner-4.jpg 2500w, https://www.lapointcamps.com/wp-content/uploads/2016/04/7-1200x297.jpg 1200w, https://www.lapointcamps.com/wp-content/uploads/2016/04/Blogbanner-4.jpg 770w" sizes="(max-width: 2500px) 100vw, 2500px">
	</div>


	<div class="blog-content container row main-mt">
		<div class="col-blog-left">
			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part('template-parts/content', 'single'); ?>

			<?php endwhile; ?>

			<div class="components-container">
				<?php
				while (have_posts()) : the_post();
					global $module_controller;
					$module_controller->render_page($post->ID);
				endwhile;
				?>
			</div>
		</div>

		<?php if (is_active_sidebar('posts-sidebar')) : ?>
			<div class="col-blog-right">
				<aside id="secondary" class="sidebar widget-area" role="complementary">
					<?php dynamic_sidebar('posts-sidebar'); ?>
				</aside>
			</div>
		<?php endif; ?>

	</div>


	<?php // get_sidebar( 'content-bottom' ); ?>

<?php get_footer(); ?>