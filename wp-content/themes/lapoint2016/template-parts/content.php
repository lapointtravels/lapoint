<?php
/**
 * The template part for displaying content
 *
 * @package WordPress
 * @subpackage Lapoint2016
 * @since Lapoint2016 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php if ( is_sticky() && is_home() && ! is_paged() ) : ?>
			<span class="sticky-post"><?php _e( 'Featured', 'lapoint' ); ?></span>
		<?php endif; ?>

		<?php the_title(sprintf('<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url(get_permalink())), '</a></h2>'); ?>
	</header>

	<?php if ( has_excerpt() || is_search() ) : ?>
		<div>
			<?php the_excerpt(); ?>
		</div>
	<?php endif; ?>

	<?php if (is_singular()) : ?>
		<div class="post-thumbnail">
			<?php the_post_thumbnail(); ?>
		</div>
	<?php else : ?>
		<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true">
			<?php the_post_thumbnail( 'post-thumbnail', array( 'alt' => the_title_attribute( 'echo=0' ) ) ); ?>
		</a>
	<?php endif;  ?>

	<div class="entry-content">
		<?php
			/* translators: %s: Name of current post */
			the_content(sprintf(
				__('Continue reading<span class="screen-reader-text"> "%s"</span>', 'lapoint'),
				get_the_title()
			));

			wp_link_pages(array(
				'before'      => '<div class="page-links"><span class="page-links-title">' . __('Pages:', 'lapoint') . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
				'pagelink'    => '<span class="screen-reader-text">' . __( 'Page', 'lapoint' ) . ' </span>%',
				'separator'   => '<span class="screen-reader-text">, </span>',
			));
		?>
	</div>

	<footer class="entry-footer">
		<?php // lapoint_entry_meta(); ?>
		<?php
			edit_post_link(
				sprintf(
					/* translators: %s: Name of current post */
					__( 'Edit<span class="screen-reader-text"> "%s"</span>', 'lapoint' ),
					get_the_title()
				),
				'<span class="edit-link">',
				'</span>'
			);
		?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
