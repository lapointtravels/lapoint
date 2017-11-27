
	<div class="<?php if (!$this->full_width) echo "container"; ?>">

		<?php if ($this->label) { ?>
			<h2><?php echo $this->label; ?></h2>
		<?php } ?>

		<?php if ($this->post->post_content) { ?>
			<p><?php echo $this->post->post_content; ?></p>
		<?php } ?>

		<div class="box-area">

			<div class="row row-tight">
				<?php foreach ($this->boxes as $box) : ?>

					<?php if ($box->post->post_type == "area-text-box") : ?>

						<div class="col-sm-6 col-md-4 mbs">
							<div class="box">
								<h3><?php echo $box->post->post_title; ?></h3>
								<p><?php echo $box->post->post_content; ?></p>

								<?php if ($box->button_text) : ?>
									<a href="<?php echo $box->formatted_button_link; ?>" class="btn btn-inverted"><?php echo $box->button_text; ?></a>
								<?php endif; ?>
							</div>
						</div>

					<?php elseif ($box->post->post_type == "area-preview-box") : ?>

						<div class="preview-box col-sm-<?php echo (6 * $box->cols_sm); ?> col-md-<?php echo (4 * $box->cols_md); ?> mbs <?php if ($box->rows_md == 2) echo "row-span-md"; ?> <?php if ($box->rows_sm == 2) echo "row-span-sm"; ?>">

							<div class="inner responsive-image"<?php
							if ($box->background_image) :
								echo " style=\"background-image: url('". $box->background_image->sizes->{"box-sm"}->url . "');\"";
								if ($box->cols_md > 1):
									echo " data-src-md='". $box->background_image->sizes->{"box-md"}->url ."'";
								endif;
								echo " data-src-sm='". $box->background_image->sizes->{"box-sm"}->url ."'";
								echo " data-src-xs='". $box->background_image->sizes->{"box-md"}->url ."'";
							endif;
							?>>
								<h4><?php
								//echo apply_filters('wpml_translate_single_string', $box->post->post_title, 'Lapoint - Box Area', 'Preview Box - Title' );
								echo $box->post->post_title;
								?></h4>
								<div class="overlay">
									<div>
										<a href="<?php echo $box->formatted_button_link; ?>" class="btn btn-inverted"><?php echo $box->button_text; ?></a>
									</div>
								</div>
							</div>

						</div>

					<?php endif; ?>

				<?php endforeach; ?>
			</div>

		</div>

	</div>