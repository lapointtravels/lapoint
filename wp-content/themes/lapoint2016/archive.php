<?php
/**
 * The template for displaying Archive pages
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each specific one. For example, Twenty Fourteen
 * already has tag.php for Tag archives, category.php for Category archives,
 * and author.php for Author archives.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage AvanTheme
 * @since AvanTheme 1.0
 */

get_header(); ?>

	<div class="generic-header"></div>

	<div class="blog-content container row main-mt">
		<div class="row">
			<div class="col-sm-8 main-blog-page">

				<h1 class="two-col-title mbm"><?php
					if ( is_day() ) :
						printf( __( 'Day: %s', 'avantheme' ), get_the_date() );

					elseif ( is_month() ) :
						printf( __( 'Month: %s', 'avantheme' ), get_the_date( _x( 'F Y', 'monthly archives date format', 'avantheme' ) ) );

					elseif ( is_year() ) :
						printf( __( 'Year: %s', 'avantheme' ), get_the_date( _x( 'Y', 'yearly archives date format', 'avantheme' ) ) );

					elseif (is_author()) :
						$author = get_userdata( get_query_var('author') );
						printf(__('Archive for %s', 'avantheme'), $author->display_name);

					else :
						_e( 'Archive', 'avantheme' );


					endif;
				?></h1>

				<?php
				if (have_posts()) :
					while ($wp_query->have_posts()) : $wp_query->the_post();
						get_template_part('template-parts/content-post', 'sm');
					endwhile;
				endif;


				$prev_page = get_previous_posts_link('<i class="fa fa-fw fa-arrow-left"></i> Föregående sida','');
				$next_page = get_next_posts_link('Nästa sida <i class="fa fa-fw fa-arrow-right"></i>');
				if ($prev_page || $next_page) : ?>
					<div class="blog-navigation">
						<p class="clearfix">
							<?php echo $prev_page; ?>
							<span class="pull-right"><?php echo $next_page; ?></span>
						</p>
					</div>
					<?php
				endif;
			?>
			</div>
			<div class="col-sm-4">
				<?php if ( is_active_sidebar('posts-sidebar')) : ?>
					<?php dynamic_sidebar('posts-sidebar'); ?>
				<?php endif; ?>
			</div>
		</div>
	</div>

<?php get_footer(); ?>
