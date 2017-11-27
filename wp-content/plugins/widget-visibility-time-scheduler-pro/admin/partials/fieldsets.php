<?php

/**
 * Provide a dashboard view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://stehle-internet.de/downloads/widget-visibility-time-scheduler-pro
 * @since      1.0.0
 *
 * @package    Hinjiwvtsp
 * @subpackage Hinjiwvtsp/admin/partials
 */
?>
<div class="wvtsp-container wvtsp-collapsed">
	<div class="wvtsp-scheduler">
		<h4><?php _e( 'Visibility Time Scheduler', 'hinjiwvtsp' ); ?></h4>
<?php $field_id = $widget->get_field_id( 'mode' ); ?>
		<p>
			<label for="<?php echo $field_id; ?>"><?php $text = 'Schedule'; _e( $text );?>:</label>
			<select id="<?php echo $field_id; ?>" name="<?php echo $this->plugin_slug; ?>[mode]">
				<option value=""><?php $text = '&mdash; Select &mdash;'; _e( $text );?></option>
<?php
foreach ( $this->modes as $mode ) {
?>
				<option value="<?php echo $mode; ?>"<?php selected( $mode, $this->scheduler[ 'mode' ] );?>><?php _e( $mode );?></option>
<?php
}
?>
			</select>
		</p>
		<fieldset>
			<legend><?php _e( 'from', 'hinjiwvtsp' ); ?></legend>
			<p><?php $this->touch_time( 'widget-start' ); ?></p>
		</fieldset>
		<fieldset>
			<legend><?php _e( 'to', 'hinjiwvtsp' ); ?></legend>
			<p><?php $this->touch_time( 'widget-end' ); ?></p>
<?php
// show advice and delete flag if user typed in an end year later than 2037
if ( false !== get_transient( $this->plugin_slug ) ) {
?>
		<p><?php _e( 'Why only up to end of 2037? Read <a href="http://en.wikipedia.org/wiki/Year_2038_problem" lang="en">Wikipedia: Year 2038 problem</a>.', 'hinjiwvtsp' ); ?></p>
<?php
	delete_transient( $this->plugin_slug );
}
?>
		</fieldset>
		<fieldset>
			<legend><?php _e( 'on', 'hinjiwvtsp' ); ?></legend>
			<p>
	<?php
			foreach ( $this->weekdays as $i => $values ) {
				$field_id = $widget->get_field_id( $values[ 'name' ] );
				$wkd = __( $values[ 'name' ] ); ?>
				<span class="wvtsp-weekday"><input class="checkbox" type="checkbox" <?php checked( in_array( $i, $this->scheduler[ 'daysofweek' ] ) ); ?> id="<?php echo $field_id; ?>" name="<?php echo $this->plugin_slug; ?>[daysofweek][]" value="<?php echo $i; ?>" /><label for="<?php echo $field_id; ?>"><?php echo $wkd; ?></label></span>
				<span class="wvtsp-daytime"><?php
				$this->touch_weekdays_time( 'start', $values[ 'abbr' ], $wkd ); ?> &ndash; <?php
				$this->touch_weekdays_time( 'end', $values[ 'abbr' ], $wkd ); ?></span><br />
	<?php
			}
	?>
			</p>
		</fieldset>
	</div><!-- .wvtsp-scheduler -->
	<p><a href="#" class="button wvtsp-link"><?php _e( 'Open scheduler', 'hinjiwvtsp' ); ?></a></p>
</div><!-- .wvtsp-container -->
