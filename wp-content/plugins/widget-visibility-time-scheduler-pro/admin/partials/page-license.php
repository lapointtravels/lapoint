<?php

/**
 * Provide a page to add a new post list
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://stehle-internet.de/
 * @since      1.0.0
 *
 * @package    Widget_Visibility_Time_Scheduler_Pro
 * @subpackage Widget_Visibility_Time_Scheduler_Pro/admin/partials
 */
?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<?php include_once 'options-head.php'; // print out success or error messages from the Settings API ?>
	<div class="wvtsp_wrapper">
		<div id="wvtsp_main">
			<div class="wvtsp_content">
				<h2><?php esc_html_e( 'License Settings', 'hinjiwvtsp' ); ?></h2>
				<form method="post" action="options.php">
<?php settings_fields( $this->license_settings_fields_slug); ?> 
					<table class="form-table">
						<tbody>
							<tr>	
								<th scope="row">
									<label for="<?php echo $this->license_key_option_name; ?>"><?php esc_html_e( 'License Key', 'hinjiwvtsp' ); ?></label>
								</th>
								<td>
									<input id="<?php echo $this->license_key_option_name; ?>" name="<?php echo $this->license_key_option_name; ?>" type="text" class="regular-text" value="<?php esc_attr_e( $license_key ); ?>" />
									<p class="description"><?php esc_html_e( 'Enter your license key. Then click on the button.', 'hinjiwvtsp' ); ?></p>
								</td>
							</tr>
<?php 
if ( ! empty( $license_key ) ) {
?>
							<tr>	
								<th scope="row">
									<?php esc_html_e( 'Licence Status', 'hinjiwvtsp' ); ?>
								</th>
								<td>
<?php 	
	// print feedback
	if ( 'valid' == $license_status ) {
?>
									<p class="wvtsp_valid"><?php echo $msg;?></p>
									<p><?php  /* translation: 1: date, 2: time */
										printf( 
											esc_html__( 'The license will expire on %1$s at %2$s.', 'hinjiwvtsp' ),
											date_i18n( get_option( 'date_format' ), $timestamp ), 
											date_i18n( get_option( 'time_format' ), $timestamp ) 
										); ?></p>
									<p><?php printf( esc_html__( 'There are %d activations left', 'hinjiwvtsp' ), $activations_left ); ?></p>
									<p><input type="submit" class="button-secondary" name="<?php echo $this->license_deactivation_action_name;?>" value="<?php esc_attr_e( 'Deactivate License', 'hinjiwvtsp' ); ?>"/></p>
									<?php wp_nonce_field( $this->license_deactivation_action_name, $this->nonce_field_name ); ?>
									<p class="description"><?php esc_html_e('Click to deactivate the license if you do not want to use it on this server.', 'hinjiwvtsp' ); ?></p>
<?php
	} elseif ( 'expired' == $license_status ) {
?>
									<p class="wvtsp_invalid"><?php echo $msg;?></p>
									<p><?php /* translation: 1: date, 2: time */
										printf( 
											esc_html__( 'The license expired on %1$s at %2$s.', 'hinjiwvtsp' ),
											date_i18n( get_option( 'date_format' ), $timestamp ), 
											date_i18n( get_option( 'time_format' ), $timestamp ) 
										); ?></p>
									<p><?php printf( esc_html__( 'There are %d activations left', 'hinjiwvtsp' ), $activations_left ); ?></p>
									<p><a href="<?php printf( '%s/checkout/?edd_license_key=%s', $this->plugin_shop_url, $license_key ); ?>"><?php esc_html_e( 'Click here for a new license', 'hinjiwvtsp' ); ?></a>.</p>
<?php
	} else {
?>
									<p class="wvtsp_invalid"><?php echo $msg;?></p>
									<p><input type="submit" class="button-secondary" name="<?php echo $this->license_activation_action_name;?>" value="<?php esc_attr_e( 'Activate License', 'hinjiwvtsp' ); ?>"/></p>
									<?php wp_nonce_field( $this->license_activation_action_name, $this->nonce_field_name ); ?>
									<p class="description"><?php esc_html_e( 'Click to activate the license after you have entered your license key.', 'hinjiwvtsp' ); ?></p>
<?php
	} // if ( 'valid' == $license_status )
?>
								</td>
							</tr>
<?php
} // if ( ! empty( $license_key ) )
?>
						</tbody>
					</table>	
					<?php submit_button(); ?> 
				</form>
				<h2><?php esc_html_e( 'Important advices about the license', 'hinjiwvtsp' ); ?></h2>
				<h3><?php esc_html_e( 'Why a license?', 'hinjiwvtsp' ); ?></h3>
				<p><?php esc_html_e( 'With activating the license you will receive automatic upgrades of the plugin for 365 days since the day of the purchase. Each license key is valid for one installation of the plugin only.', 'hinjiwvtsp' ); ?></p>
				<h3><?php esc_html_e( 'Terms of the license', 'hinjiwvtsp' ); ?></h3>
				<p>
					<?php esc_html_e( 'By activating this license you are also confirming your agreement to be bound by the terms of the license associated with this plugin which you acknowledged at the time of the purchase checkout.', 'hinjiwvtsp' ); ?>
					<a href="<?php echo esc_url( __( 'https://shop.stehle-internet.de/informations/terms-licence-withdrawal/', 'hinjiwvtsp' ) ); ?>" target="_blank"><?php esc_html_e( 'Read the terms of the license (in new window)', 'hinjiwvtsp' ); ?></a>.
				</p>
				<p><?php esc_html_e( 'This includes that the warranty offered by the plugin author is limited to correcting any defects and that the plugin author will not be held liable for any actions or financial loss occurring as a result of using this plugin.', 'hinjiwvtsp' ); ?></p>
				<h3><?php esc_html_e( 'Contact', 'hinjiwvtsp' ); ?></h3>
				<p>
					<?php esc_html_e( 'If you have any issues and problems with activating you can contact the plugin author for solutions.', 'hinjiwvtsp' ); ?>
					<a href="<?php echo esc_url( __( 'https://shop.stehle-internet.de/contact/', 'hinjiwvtsp' ) ); ?>" target="_blank"><?php esc_html_e( 'Contact page (in new window)', 'hinjiwvtsp' ); ?></a>.
				</p>

<?php include_once( 'section-footer.php' ); ?>