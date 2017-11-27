
	<?php if (!$this->full_width) : ?>
		<div class="container">
	<?php endif; ?>

		<div class="intro-section">

			<div class="container">

				<?php if ($this->post->post_title) : ?>
					<h1 class="center"><?php echo $this->post->post_title; ?></h1>
				<?php endif; ?>
				<?php if ($this->post->post_content) : ?>
					<div class="ingress center">
						<p><?php echo $this->post->post_content; ?></p>
					</div>
				<?php endif; ?>

				<div class="sub-container">
					<div class="content-row row">
						<div class="col-sm-6">
							<div class="inner">
								<?php if ($this->col1_title) : ?>
									<h2><?php echo $this->col1_title; ?></h2>
								<?php endif; ?>
								<?php if ($this->col1_content) : ?>
									<p><?php echo $this->col1_content; ?></p>
								<?php endif; ?>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="inner">
								<?php if ($this->col2_title) : ?>
									<h2><?php echo $this->col2_title; ?></h2>
								<?php endif; ?>
								<?php if ($this->col2_content) : ?>
									<p><?php echo $this->col2_content; ?></p>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>

			</div>

		</div>

	<?php if (!$this->full_width) : ?>
		</div>
	<?php endif; ?>
