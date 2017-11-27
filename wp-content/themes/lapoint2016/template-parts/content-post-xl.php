<div class="xl-news-post">
	<div class="container row">
		<div class="container row">
			<div class="col-sm-8">
				<div class="post-image">
					<?php the_post_thumbnail('large-thumb'); ?>
				</div>
			</div>

			<div class="col-sm-4">
				<h2><a href="<?php the_permalink(); ?>" title="Read more"><?php the_title(); ?></a></h2>
				<span class="post-date"><?php the_date(); ?></span>

				<?php the_excerpt(); ?>
			</div>
		</div>
	</div>
</div>