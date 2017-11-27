
	<div class="dark <?php if (!$this->full_width) echo " container"; ?>">

		<?php if ($this->label) : ?>
			<h2 class="center mbl"><?php echo $this->label; ?></h2>
		<?php endif; ?>

		<div class="tabs">

			<ul class="clearfixx">
				<?php
				$count = 0;
				foreach ($this->tabs as $tab) :
					$count++; ?>
					<li><a href="#tab-<?php echo $count; ?>"><?php echo $tab->title; ?></a></li>
				<?php endforeach; ?>
			</ul>

			<?php
			$count = 0;
			foreach ($this->tabs as $tab) :
				$count++; ?>
				<div id="tab-<?php echo $count; ?>">
					<?php foreach ($tab->components as $kmc_component) : ?>
						<div class="kmc-component kmc-component-<?php echo $kmc_component->post->post_type; ?>">
							<?php echo $kmc_component->render(); ?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endforeach; ?>

		</div>

	</div>
