<?php
global $title;
add_meta_box("collecta_content", $title, "collecta_user_list_meta_box", "collecta_user_list", "normal", "core");	
?>

<div class="wrap">

	<div id="collecta-settings-container" class="metabox-holder">
		<?php do_meta_boxes('collecta_user_list','normal', null); ?>
	</div>

</div>


<?php
function collecta_user_list_meta_box($post, $metabox){
	global $wpdb, $totalCount, $users;
	?>

	There are <?php echo $totalCount; ?> registered users.

	<table id="collect-user-table" data-total-count="<?php echo $totalCount; ?>" cellpadding="0" cellspacing="1">
		<thead>
			<tr>
				<th class="sort asc" data-sort="name" style="width: 25%;">Name</th>
				<th data-sort="email" style="width: 25%;">Email</th>
				<th data-sort="lang" style="width: 10%;">Language</th>
				<th data-sort="ip" style="width: 20%;">IP</th>
				<th data-sort="created" style="width: 20%;">Timestamp</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($users as $user){ ?>
			<tr>
				<td><?php echo $user->name; ?></td>
				<td><?php echo $user->email; ?></td>
				<td><?php echo $user->lang; ?></td>
				<td><?php echo $user->ip; ?></td>
				<td><?php echo $user->created; ?></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>

	<div id="collecta-user-pagination"></div>

	<?php
} ?>