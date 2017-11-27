<?php
/**
 * The template part for displaying single posts
 *
 * @package WordPress
 * @subpackage Lapoint2016
 * @since Lapoint2016 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	</header>

	<?php
	/*the_excerpt(); ?>

	<div class="post-thumbnail">
		<?php the_post_thumbnail(); ?>
	</div>
	*/ ?>

	<div class="entry-content">
		<?php
			the_content();

			wp_link_pages( array(
				'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'lapoint' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
				'pagelink'    => '<span class="screen-reader-text">' . __( 'Page', 'lapoint' ) . ' </span>%',
				'separator'   => '<span class="screen-reader-text">, </span>',
			) );

			/*if ( '' !== get_the_author_meta( 'description' ) ) {
				get_template_part( 'template-parts/biography' );
			}*/
		?>
	</div>

	<footer class="entry-footer">
		<?php // lapoint_entry_meta(); ?>
	</footer>
</article>
