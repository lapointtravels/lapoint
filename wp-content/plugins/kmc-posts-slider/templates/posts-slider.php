<?php if ($this->post->post_title) : ?>
	<h2><?php echo $this->post->post_title; ?></h2>
<?php endif; ?>

<div class="posts-container">

	<div class="posts-slider">
		<?php
		$post_query = new WP_Query(array(
		    'post_type' => 'post',
			'posts_per_page' => 20,
			'post_status' => 'publish'
		));
		if ($post_query->have_posts()) :
			while ($post_query->have_posts()) :
				$post_query->the_post();
				global $post;
				?>

					<?php
					$hasThumb = has_post_thumbnail($post->ID);
					?>
					<article class="post-slide <?php if (!$hasThumb) { echo 'no-image'; } ?>">
						<?php if ($hasThumb) : ?>
							<div class="post-image">
								<?php
								$small_thumb = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'rect-sm');
								$medium_thumb = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'rect-md');
								?>
								<img src="<?php echo $small_thumb[0]; ?>"
									srcset="<?php echo $medium_thumb[0]; ?> 640w, <?php echo $small_thumb[0]; ?> 320w"
									sizes="(min-width: 800px) 294px, 100vw"
									alt="<?php the_title(); ?>">
							</div>
						<?php endif; ?>
						<header>
							<div class="post-date">
								<span class="date"><?php the_time('d'); ?></span>
								<span class="month"><?php the_time('M'); ?></span>
							</div>
							<h2><a href="<?php the_permalink(); ?>" title="Read more"><?php the_title(); ?></a></h2>
						</header>
						<div class="body">
							<?php the_excerpt(); ?>
						</div>
					</article>

		    	<?php
		  	endwhile;
		endif;
		wp_reset_postdata();
		?>
		<a href="#" class="prev-link"><i class="icon icon-arrow-left"></i></a>
		<a href="#" class="next-link"><i class="icon icon-arrow-right"></i></a>
	</div>
</div>