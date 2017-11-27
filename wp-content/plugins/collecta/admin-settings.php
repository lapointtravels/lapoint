<?php
global $title, $screen_layout_columns;
add_meta_box("collecta_content", $title, "collecta_meta_box", "collecta_settings", "normal", "core");	
?>

<div class="wrap">

	<div id="collecta-settings-container" class="metabox-holder">
		<?php do_meta_boxes('collecta_settings','normal', null); ?>
	</div>

</div>


<?php
function collecta_meta_box($post, $metabox){
global $wpdb;
?>

	<form name="collecta-settings-form" action="<?php echo admin_url('admin.php') .'?page=collecta-settings'; ?>" method="post">
		<input type="hidden" name="admin-action" value="update-settings" />

		<?php 
		if (function_exists(icl_get_languages)){
			$languages = icl_get_languages('skip_missing=0&orderby=code');
			if (!empty($languages)){
				foreach($languages as $l){
					$lang = $l['language_code'];
					displayOptions(array(
						'title' => $l['translated_name'],
						'language' => $l['language_code']
					));
			    }
			}
		} else {
			displayOptions(array(
				'title' => '',
				'language' => ''
			));
		}
		?>

		<input type="submit" class="button-primary" value="Update settings" />

	</form>

<?php }


function displayOptions($args){
	$code = '-'. $args['language']; ?>

	<h4 class="collecta-settings-title"><?php echo $args["title"]; ?></h4>

	<ul class="collecta-settings-list">
		<li>
			<label for="collecta-name-placeholder<?php echo $code; ?>">Name placeholder text:</label>
			<input type="text" name="collecta-name-placeholder<?php echo $code; ?>" value="<?php echo get_option('collecta-name-placeholder'. $code); ?>">
		</li>
		<li>
			<label for="collecta-email-placeholder<?php echo $code; ?>">Email placeholder text:</label>
			<input type="text" name="collecta-email-placeholder<?php echo $code; ?>" value="<?php echo get_option('collecta-email-placeholder'. $code); ?>">
		</li>
		<li>
			<label for="collecta-thanks<?php echo $code; ?>">Thanks message:</label>
			<input type="text" name="collecta-thanks<?php echo $code; ?>" value="<?php echo get_option('collecta-thanks'. $code); ?>">
		</li>
		<li>
			<label for="collecta-email-to<?php echo $code; ?>">Send registrations to email:</label>
			<input type="text" name="collecta-email-to<?php echo $code; ?>" value="<?php echo get_option('collecta-email-to'. $code); ?>">
		</li>
		<li>
			<label for="collecta-email-sender<?php echo $code; ?>">Registration sender:</label>
			<input type="text" name="collecta-email-sender<?php echo $code; ?>" value="<?php echo get_option('collecta-email-sender'. $code); ?>">
		</li>
		<li>
			<label for="collecta-email-sender-mail<?php echo $code; ?>">Registration sender email:</label>
			<input type="text" name="collecta-email-sender-mail<?php echo $code; ?>" value="<?php echo get_option('collecta-email-sender-mail'. $code); ?>">
		</li>
		<li>
			<label for="collecta-email-subject<?php echo $code; ?>">Registration email subject:</label>
			<input type="text" name="collecta-email-subject<?php echo $code; ?>" value="<?php echo get_option('collecta-email-subject'. $code); ?>">
		</li>
		<li>
			<label for="collecta-email-body<?php echo $code; ?>">Registration mail body:</label>
			<textarea name="collecta-email-body<?php echo $code; ?>"><?php echo get_option('collecta-email-body'. $code); ?></textarea>
		</li>
	</ul>

	<?php

} ?>