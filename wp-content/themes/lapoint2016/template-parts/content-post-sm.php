<?php
$hasThumb = has_post_thumbnail($post->ID);
?>
<div class="small-post <?php if (!$hasThumb) { echo 'no-image'; } ?>" data-animated="slide-up-fade">
	<?php if ($hasThumb) : ?>
		<div class="post-image">
			<?php
			$imageSm = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'rect-sm' );
			$imageLarge = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'rect-md' );
			?>
			<div class="image responsive-image" data-src-xs="<?php echo $imageLarge[0]; ?>" data-src-default="<?php echo $imageSm[0]; ?>"></div>
		</div>
	<?php endif; ?>

	<div class="body">
		<h3><a href="<?php the_permalink(); ?>" title="Read more"><?php the_title(); ?></a></h3>
		<span class="post-date"><?php the_date(); ?></span>

		<?php the_excerpt(); ?>
	</div>
</div>