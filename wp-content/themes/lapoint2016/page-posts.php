<?php
/**
 * Template Name: News page
 *
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


	<?php
	$args = array(
		'post_type' => 'post',
		'posts_per_page' => 10,
		'post_status' => 'publish',
		'paged' => get_query_var('paged', 1),
		'suppress_filters' => false
	);
	$query = new WP_Query( $args );


	if ($query->have_posts()) :
		$query->the_post();
		get_template_part('template-parts/content-post', 'xl');
	endif;
	?>


	<div class="blog-content container row main-mt">
		<div class="col-blog-left">
			<?php
			if ($query->have_posts()) :

				while ($query->have_posts()) : $query->the_post();
					get_template_part('template-parts/content-post', 'sm');
				endwhile;

			endif;
			?>

  			<div class="post-nav">
				<?php next_posts_link( __( '&larr; Older posts', 'lapoint' ), $query->max_num_pages ); ?>
				<span class="pull-right">
					<?php previous_posts_link( __( 'Newer posts &rarr;', 'lapoint' ) ); ?>
				</span>
			</div>

			<?php
			wp_reset_postdata();
			?>
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
