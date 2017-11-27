
	<?php if (!$this->full_width) : ?>
		<div class="container">
	<?php endif; ?>

		<div class="center parallax-image quote-section-box" <?php
		if ($this->image) :
			//echo 'style="background-image:url(\'' . $this->image->sizes->{"full"}->url . '\');"';
		 	echo ' data-parallax="scroll" data-image-src="' . $this->image->sizes->full->url . '"';
		endif; ?> data-animated="true">

			<?php /*if ($this->image) : ?>
				<div class="image"><img src="<?php echo $this->image->sizes->full->url; ?>" /></div>
			<?php endif;*/ ?>

			<div class="inner">
				<?php if ($this->post->post_title) : ?>
					<strong class="quote"><?php echo $this->post->post_title; ?></strong>
				<?php endif; ?>

				<?php if ($this->name) : ?>
					<p class="quote-name"><?php echo $this->name; ?></p>
				<?php endif; ?>
			</div>
		</div>

	<?php if (!$this->full_width) : ?>
		</div>
	<?php endif; ?>
