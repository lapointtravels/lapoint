<?php
global $LOCATION, $DESTINATION, $PACKAGE;
if ($LOCATION) :
	$camps = $LOCATION->get_camps();
elseif ($PACKAGE) :
	$camps = $PACKAGE->get_camps();
else :
	$camps = $DESTINATION->get_camps();
endif;
?>

	<div class="<?php if (!$this->full_width) echo " container"; ?>">

		<?php if ($this->post->post_title || $this->post->post_content) : ?>

			<header class="center">
				<?php if ($this->post->post_title) : ?>
					<h2><?php echo $this->post->post_title; ?></h2>
				<?php endif; ?>

				<?php if ($this->post->post_content) : ?>
					<div class="ingress wide">
						<p><?php echo $this->post->post_content; ?></p>
					</div>
				<?php endif; ?>
			</header>

		<?php endif; ?>


		<div class="camps-container">
			<?php foreach ($camps as $camp): ?>
				<article class="camp-row row" data-animated="slide-up-fade">

					<div class="col-sm-6">
						<?php if ($camp->box_background_image) : ?>
							<span class="image-link">
								<img src="<?php echo $camp->box_background_image["sizes"]["rect-md"]; ?>">
								<div class="overlay">
									<div>
										<a href="<?php echo $camp->link; ?>" class="btn btn-inverted"><?php echo __("Show", "lapoint"); ?></a>
									</div>
								</div>
							</span>
						<?php elseif (has_post_thumbnail($camp->id)) : ?>
							<span class="image-link">
								<?php echo get_the_post_thumbnail($camp->id, 'rect-md'); ?>
								<div class="overlay">
									<div>
										<a href="<?php echo $camp->link; ?>" class="btn btn-inverted"><?php echo __("Show", "lapoint"); ?></a>
									</div>
								</div>
							</span>
						<?php endif; ?>
					</div>
					<div class="col-sm-6">
						<div class="inner">
							<?php if ($camp->title) : ?>
								<h3><?php echo $camp->title; ?></h3>
							<?php endif; ?>

							<?php if ($camp->excerpt) : ?>
								<p><?php echo $camp->excerpt; ?></p>
							<?php endif; ?>

							<p class="mtm"><?php echo $camp->info_list; ?></p>

							<a href="<?php echo $camp->link; ?>" class="btn btn-secondary mtm"><?php
								if ($camp->button_text == "DEFAULT" || !$camp->button_text) :
									echo __("Show", "lapoint");
								else:
									echo $camp->button_text;
								endif;
							?></a>
						</div>

					</div>

				</article>
			<?php endforeach ?>
		</div>

	</div>
