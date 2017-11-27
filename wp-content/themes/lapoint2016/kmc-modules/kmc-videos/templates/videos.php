<?php
global $videos_manager;
require_once('modal-video-template.php');
?>

<?php /*if ($this->full_width) : ?>
	<div>
<?php else : ?>
	<div>
<?php endif;*/ ?>
<div>

	<header>
		<?php if ($this->post->post_title) : ?>
			<h2 class="center"><?php echo $this->post->post_title; ?></h2>
		<?php endif; ?>

		<?php if ($this->post->post_content) : ?>
			<div class="ingress wide">
				<p><?php echo $this->post->post_content; ?></p>
			</div>
		<?php endif; ?>
	</header>


	<?php if ($this->videos): ?>
		<div class="videos-slider clearfix">
			<div class="wrapper">
				<?php foreach ($this->videos as $video): ?>
					<?php if ($video->youtube_url && $video->width && $video->height) : ?>
						<div class="video-slide">
							<div class="img"
								data-video-url="<?php echo $video->youtube_url; ?>"
								data-width="<?php echo $video->width; ?>"
								data-height="<?php echo $video->height; ?>"
								data-autoplay="<?php echo $video->autoplay; ?>">

								<div class="spinner">
									<div class="bounce1"></div>
									<div class="bounce2"></div>
									<div class="bounce3"></div>
								</div>

								<img src="<?php echo $video->thumb[0]; ?>" width="300" height="200" />

							</div>
							<h4><?php echo $video->title; ?></h4>
						</div>
					<?php endif; ?>
				<?php endforeach ?>
			</div>

			<div class="nav">
				<a href="#" class="prev-link"><i class="icon icon-arrow-left"></i></a>
				<a href="#" class="next-link"><i class="icon icon-arrow-right"></i></a>
			</div>
		</div>

	<?php endif; ?>

</div>
