
	<?php if (!$this->full_width) : ?>
		<div class="container">
	<?php endif; ?>

		<div class="info-box animate-directly icon-<?php echo $this->icon; ?>">
			<h1><?php echo $this->post->post_title; ?></h1>

			<div class="divider"></div>

			<p><?php echo $this->post->post_content; ?></p>

			<?php if ($this->button_text) : ?>
				<div class="btns">
					<a href="<?php echo $this->button_link; ?>" class="btn btn-cta btn-primary"><?php echo $this->button_text; ?></a>
				</div>
			<?php endif; ?>
		</div>

	<?php if (!$this->full_width) : ?>
		</div>
	<?php endif; ?>
