<?php

/**
 * Provide the footer of an admin page
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
			</div><!-- .wvtsp_content -->
		</div><!-- #wvtsp_main -->
		<div id="wvtsp_footer">
			<div class="wvtsp_content">
				<h2><?php esc_html_e( 'Helpful Links', 'hinjiwvtsp' ); ?></h2>
				<p><?php
				$link = sprintf( '<a href="https://shop.stehle-internet.de/reviews/">%s</a>', esc_html__( 'Reviews', 'hinjiwvtsp' ) );
				esc_html_e( 'Do you like the plugin?', 'hinjiwvtsp' ); ?>
				<?php printf( esc_html__( 'Rate it on %s.', 'hinjiwvtsp' ), $link );
				?></p>
				<p>&copy; 2016 <a href="http://stehle-internet.de/">Martin Stehle</a>, Berlin, Germany</p>
			</div><!-- .wvtsp_content -->
		</div><!-- #wvtsp_footer -->
	</div><!-- .wvtsp_wrapper -->
</div><!-- .wrap -->
