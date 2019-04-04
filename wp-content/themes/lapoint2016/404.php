<?php
/**
 * @package WordPress
 * @subpackage Lapoint2016
 * @since Lapoint2016 1.0
 */

header("HTTP/1.0 404 Not Found");

get_header(); ?>

	<div class="full-image">
		<img width="2500" height="618" src="//www.lapointcamps.com/wp-content/uploads/2016/04/7-2500x618.jpg" class="attachment-header-image size-header-image wp-post-image" alt="080316_0860" srcset="//www.lapointcamps.com/wp-content/uploads/2016/04/7-300x74.jpg 300w, //www.lapointcamps.com/wp-content/uploads/2016/04/7-768x190.jpg 768w, //www.lapointcamps.com/wp-content/uploads/2016/04/7-1024x253.jpg 1024w, //www.lapointcamps.com/wp-content/uploads/2016/04/7-2500x618.jpg 2500w, //www.lapointcamps.com/wp-content/uploads/2016/04/7-1200x297.jpg 1200w, www.lapointcamps.com/wp-content/uploads/2016/04/7-770x190.jpg 770w" sizes="(max-width: 2500px) 100vw, 2500px">
	</div>

	<div class="container mvl">

		<h1><?php _e( 'Not found', 'lapoint' ); ?></h1>
		<p><?php _e( 'It seems we can&rsquo;t find what you&rsquo;re looking for...', 'lapoint' ); ?></p>

	</div>

<?php get_footer(); ?>
