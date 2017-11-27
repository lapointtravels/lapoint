
	<?php if (!$this->full_width) : ?>
		<div class="container">
	<?php endif; ?>

		<?php echo apply_filters('the_content', $this->post->post_content); ?>

	<?php if (!$this->full_width) : ?>
		</div>
	<?php endif; ?>

