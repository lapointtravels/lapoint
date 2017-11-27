
	<?php if (!$this->full_width) : ?>
		<div class="container">
	<?php endif; ?>

		<div class="row">

			<div class="col col-sm-6">
				<?php if ($this->image) : ?>
					<div class="image" style="background-image:url('<?php echo $this->image->sizes->{"box-md"}->url; ?>');"></div>
				<?php endif; ?>
			</div>
			<div class="col col-sm-6">
				<div class="inner">
					<?php if ($this->post->post_title) : ?>
						<h2><?php echo $this->post->post_title; ?></h2>
					<?php endif; ?>

					<?php if ($this->post->post_content) : ?>
						<p><?php echo $this->post->post_content; ?></p>
					<?php endif; ?>
				</div>
			</div>

		</div>

	<?php if (!$this->full_width) : ?>
		</div>
	<?php endif; ?>
