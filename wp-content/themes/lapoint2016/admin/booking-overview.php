<?php
add_meta_box("booking_overview", "Booking overview", "lapoint_booking_overview", "lapoint_booking_overview", "normal", "core");


// ****************************** Structure ******************************
?>

<div class="wrap">
	<div id="booking-overview-container" class="metabox-holder">
		<div id="post-body" class="has-sidebar">
			<div id="post-body-content">
				<?php do_meta_boxes('lapoint_booking_overview','normal', null); ?>
			</div>
		</div>
		<br class="clear"/>
	</div>
</div>

<?php
// ****************************** Booking Overview ******************************
function lapoint_booking_overview () {
	global $destination_types_manager, $destinations_manager, $camps_manager, $levels_manager;
	$destination_types = $destination_types_manager->get_all_in_lang("en");
	$destinations = $destinations_manager->get_all_in_lang("en");
	$camps = $camps_manager->get_all_in_lang("en");
	$levels = $levels_manager->get_all_in_lang("en");
	?>

	<p class="booking-info">
		The code used when talking to the Travelize booking system is combined from four different parts; destination type, destination, camp and level.<br>
		Like this: [Destination type code]_[Destination code]_[Camp code]_[Level code]
	</p>


	<h3>Destination types</h3>
	<table class="booking-table">
		<thead>
			<tr>
				<th width="200">Name</th>
				<th>Booking code</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($destination_types as $destination_type) : ?>
				<tr data-post-id="<?php echo $destination_type->id; ?>">
					<td><a href="<?php echo HOME_URI; ?>/wp-admin/post.php?post=<?php echo $destination_type->id; ?>&action=edit"><?php echo $destination_type->title; ?></a></td>
					<td><input type="text" value="<?php echo $destination_type->booking_code; ?>" class="booking-code" /></td>
					<td class="center">
						<button class="update-code-button">Update</button>
						<span class="info-saving hidden">Saving</span>
						<span class="info-saved hidden">Saved!</span>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>


	<h3>Destinations</h3>
	<table class="booking-table">
		<thead>
			<tr>
				<th width="200">Name</th>
				<th>Booking code</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($destinations as $destination) : ?>
				<tr data-post-id="<?php echo $destination->id; ?>">
					<td>
						<a href="<?php echo HOME_URI; ?>/wp-admin/post.php?post=<?php echo $destination->id; ?>&action=edit"><?php echo $destination->title; ?></a>
						<span style="color: #999; margin-left: 10px;">(<?php echo $destination->get_type()->title; ?>)</span>
					</td>
					<td><input type="text" value="<?php echo $destination->booking_code; ?>" class="booking-code" /></td>
					<td class="center">
						<button class="update-code-button">Update</button>
						<span class="info-saving hidden">Saving</span>
						<span class="info-saved hidden">Saved!</span>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>


	<h3>Camps</h3>
	<table class="booking-table">
		<thead>
			<tr>
				<th width="200">Name</th>
				<th>Booking code</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($camps as $camp) :
				$sv_post_id = icl_object_id($camp->id, "camp", false, "sv");
				$sv_post = $camps_manager->get($sv_post_id);
				$da_post_id = icl_object_id($camp->id, "camp", false, "da");
				$da_post = $camps_manager->get($da_post_id);
				$nb_post_id = icl_object_id($camp->id, "camp", false, "nb");
				$nb_post = $camps_manager->get($nb_post_id);
				?>

				<tr data-post-id="<?php echo $camp->id; ?>" class="has-label-override">
					<td><a href="<?php echo HOME_URI; ?>/wp-admin/post.php?post=<?php echo $camp->id; ?>&action=edit"><?php echo $camp->title; ?></a></td>
					<td><input type="text" value="<?php echo $camp->booking_code; ?>" class="booking-code" /></td>
					<td class="booking-labels-container <?php
					if ($camp->booking_label || $sv_post->booking_label || $nb_post->booking_label || $da_post->booking_label) echo "open";
					?>">
						<a href="#" class="override-label-link">Override label</a>
						<label>En: <input id="booking-label-<?php echo $camp->id; ?>-en" type="text" value="<?php echo $camp->booking_label; ?>" class="booking-label en" /></label>
						<label>Sv: <input id="booking-label-<?php echo $camp->id; ?>-sv" type="text" value="<?php echo $sv_post->booking_label; ?>" class="booking-label sv" /></label>
						<label>Nb: <input id="booking-label-<?php echo $camp->id; ?>-nb" type="text" value="<?php echo $nb_post->booking_label; ?>" class="booking-label nb" /></label>
						<label>Da: <input id="booking-label-<?php echo $camp->id; ?>-da" type="text" value="<?php echo $da_post->booking_label; ?>" class="booking-label da" /></label>
					</td>
					<td class="center">
						<button class="update-code-button">Update</button>
						<span class="info-saving hidden">Saving</span>
						<span class="info-saved hidden">Saved!</span>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>


	<h3>Levels</h3>
	<table class="booking-table">
		<thead>
			<tr>
				<th width="200">Name</th>
				<th>Booking code</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($levels as $level) : ?>
				<tr data-post-id="<?php echo $level->id; ?>">
					<td><a href="<?php echo HOME_URI; ?>/wp-admin/post.php?post=<?php echo $level->id; ?>&action=edit"><?php echo $level->title; ?></a></td>
					<td><input type="text" value="<?php echo $level->booking_code; ?>" class="booking-code" /></td>
					<td class="center">
						<button class="update-code-button">Update</button>
						<span class="info-saving hidden">Saving</span>
						<span class="info-saved hidden">Saved!</span>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>


<?php } ?>