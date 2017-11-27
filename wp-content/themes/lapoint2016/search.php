<?php
/**
 * The template for displaying Search Results pages
 *
 * @package WordPress
 * @subpackage AvanTheme
 * @since AvanTheme 1.0
 */

get_header(); ?>

	<div class="row">
		<div class="col-sm-8">

			<?php if ( have_posts() ) : ?>
				<h1 class="two-col-title"><?php printf( __( 'Sökresultat för: %s', 'avantheme' ), get_search_query() ); ?></h1>

				<?php
				while (have_posts()) : the_post();
					get_template_part('content-blog', 'sm');
				endwhile;

				if( show_posts_nav() ) :
					blog_navigation();
				endif;

			else :
				// If no content, include the "No posts found" template.
				?>
				<h1 class="two-col-title">Ingen träff</h1>
				<p>Din sökning efter <i>"<?php echo get_search_query() ?>"</i> gav ingen träff. Sök efter något annat i sökrutan till höger, eller <a href="/">gå till startsidan</a></p>
				<?php

			endif;
			?>
		</div>
		<div class="col-sm-4">
			<?php if ( is_active_sidebar('page-widget-area')) : ?>
				<?php dynamic_sidebar('posts-sidebar'); ?>
			<?php endif; ?>
		</div>
	</div>

<?php get_footer(); ?>