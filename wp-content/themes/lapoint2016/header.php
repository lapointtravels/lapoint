<?php
/**
 * @package WordPress
 * @subpackage Lapoint2016
 * @since Lapoint2016 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php endif; ?>

	<!-- <script src="https://unpkg.com/current-device/umd/current-device.min.js"></script> -->

	<script src="https://wchat.freshchat.com/js/widget.js"></script>
	
	<!--[if lt IE 9]>
		<script src="<?php echo THEME_URL; ?>/js/html5.js"></script>
	<![endif]-->

	<?php
	/* <link rel="shortcut icon" type="image/x-icon" href="<?php echo THEME_URL; ?>/img/favicon.ico">; */
	/*
	global $post;
	if ($post && $post->ID) :
		$rel = get_post_meta($post->ID, 'rel_canonical', true);
	 	if ($rel) : ?>
			<link rel="canonical" href="<?php echo $rel; ?>" />
			<?php
		endif;

		$keywords = get_post_meta($post->ID, '_amt_keywords', true);
		if ($keywords) : ?>
			<meta name="keywords" content="<?php echo $keywords; ?>" />
			<?php
		endif;

		$desc = get_post_meta($post->ID, '_amt_description', true);
		if ($desc) : ?>
			<meta name="description" content="<?php echo $desc; ?>" />
			<meta property="og:description" content="<?php echo $desc; ?>" />
			<?php
		endif;

		if( $post->post_type == "post" ) : ?>
			<meta property="og:type" content="article" /> 
		<?php
		else : ?>
			<meta property="og:type" content="website" /> 			
		<?php
		endif;

		$title = get_post_meta($post->ID, '_amt_title', true);
		if ($title) : ?>
			<meta property="og:title" content="<?php echo $title; ?>" />
			<?php
		endif;

		$thumbnail = get_post_thumbnail_id();
		if ($thumbnail) : 
			$thumbnail_data = wp_get_attachment_image_src($thumbnail, 'full');
			// strip protocol. then fb debug complains about it not being a valid url
			//$thumbnail_src = str_replace( array('http://','https://'), '//', $thumbnail_data[0] );
			$thumbnail_src = $thumbnail_data[0];
			?>
			<meta property="og:image" content="<?php echo $thumbnail_src; ?>" />
			<meta property="og:image:width" content="<?php echo $thumbnail_data[1]; ?>" />
			<meta property="og:image:height" content="<?php echo $thumbnail_data[2]; ?>" />
			<?php
		endif;

		?>
			<meta property="og:url" content="<?php echo get_permalink( $post->ID ); ?>" />
		<?php
	endif;
	*/
	?>

	<?php wp_head(); ?>

	<style type="text/css">
	@media only screen and (max-width: 760px) {
		.booking-bar td:nth-of-type(1):before { content: "<?php _e("Date:", "lapoint"); ?>" }
		.booking-bar td:nth-of-type(2):before { content: "<?php _e("Spaces:", "lapoint"); ?>" }
		.booking-bar td:nth-of-type(4):before { content: "<?php _e("Basic Price:", "lapoint"); ?>"; }
	}
	</style>
</head>

<body <?php body_class(); ?>>

	<!-- Google Tag Manager -->
	<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-NSNVVK"
	height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-NSNVVK');</script>
	<!-- End Google Tag Manager -->

	<?php /*
	<div class="border: 10px solid red; padding: 100px; background-color: gold;">
		<?php echo get_post_type(); ?>
	</div>
	*/ ?>

	<?php
		$show_top_banner = intval( get_option( "tbb_show_banner" ), 10 );
	?>
	<?php if( $show_top_banner ) : ?>
		<div class="top-banner-bar">
			<?php 
				$current_lang = "en";
				switch ( ICL_LANGUAGE_CODE ) {
					case "sv":
						$current_lang = "se";
						break;
					case "nb":
						$current_lang = "no";
						break;
					case "da":
						$current_lang = "dk";
						break;
					
					default:
						$current_lang = "en";
						break;
				}			
				$banner_text = get_option( "tbb_banner_text_" . $current_lang );
			?>
			<span><?php echo $banner_text; ?></span>
		</div>
	<?php endif; ?>
	<header class="main-header">
		<div class="container">
			<a class="lapoint-logo hide-text" href="<?php echo icl_get_home_url(); ?>">Lapoint</a>
		</div>

		<nav id="menu-container">
			<div class="primary-menu">
				<div class="container">
					<?php wp_nav_menu(array('theme_location' => 'primary')); ?>
				</div>
			</div>

			<?php
			global $destination_types_manager;
			$destination_types = $destination_types_manager->get_all();
			$filtered_destination_types = array();
			$level_destination_types = array();
			foreach ($destination_types as $destination_type) :
				$destinations = $destination_type->get_destinations();
				if (count($destinations) > 0) :
					$filtered_destination_types[] = $destination_type;
				endif;
				$levels = $destination_type->get_levels();
				if (count($levels) > 0) :
					$level_destination_types[] = $destination_type;
				endif;
			endforeach;
			?>

			<div class="select-destination select-menu-dropdown">
				<div class="row clearfix">
					<?php
					foreach ($filtered_destination_types as $destination_type) :
						$destinations = $destination_type->get_destinations();
						?>
						<div class="col col-sm-4">
							<h4><a href="<?php
							echo home_url(get_page_uri($destination_type->id)) . "/destinations";
							?>"><?php
							echo $destination_type->title;
							?></a></h4>
							<ul class="destination-nav">
								<?php foreach ($destinations as $destination) : ?>
									<li>
										<a href="<?php echo $destination->link; ?>"><?php echo $destination->title; ?></a>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
						<?php
					endforeach;
					?>
				</div>
			</div>

			<div class="select-level select-menu-dropdown">
				<div class="row clearfix">
					<?php
					global $level_manager;
					foreach ($level_destination_types as $destination_type) :
						$levels = $destination_type->get_levels();
						?>
						<div class="col col-sm-4">
							<h4><a href="<?php
							echo home_url(get_page_uri($destination_type->id)) . "/levels";
							?>"><?php echo $destination_type->title; ?></a></h4>
							<ul class="destination-nav">
								<?php foreach ($levels as $level) : ?>
									<li>
										<a href="<?php echo $level->link; ?>"><?php echo $level->display_label; ?></a>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
						<?php
					endforeach;
					?>
				</div>
			</div>


			<div class="main-booking">
				<?php //include "kmc-modules/kmc-booking-bar/templates/booking-bar.php"; ?>

				<?php
				global $destination_types_manager, $destinations_manager, $camps_manager, $levels_manager;
				$destination_types = $destination_types_manager->get_all();
				$destinations = $destinations_manager->get_all();
				$camps = $camps_manager->get_all();
				$levels = $levels_manager->get_all();
				?>

					<div class="kmc-booking-bar booking-bar container clearfix" data-animated="true">
						<div class="row">
							<div class="book-choice-container">
								<select class="select book-destination-type book-choice">
									<?php
									foreach ($destination_types as $destination_type) :
										if ($destination_type->booking_code) : ?>
											<option value="<?php echo $destination_type->id; ?>" data-code="<?php echo $destination_type->booking_code; ?>"><?php echo $destination_type->title; ?></option>
											<?php
										endif;
									endforeach;
									?>
								</select>
							</div>

							<div class="book-choice-container">
								<select class="select book-destination book-choice">
									<option value=""><?php echo __("Select destination", "lapoint"); ?></option>
									<?php
									foreach ($destinations as $destination) :
										if ($destination->booking_code) : ?>
											<option value="<?php echo $destination->id; ?>" data-code="<?php echo $destination->booking_code; ?>" data-destination-type="<?php echo $destination->get_type()->id; ?>" data-levels="<?php
											if ($destination->levels) :
												foreach ($destination->levels as $level) :
													echo "-". $level->ID ."-";
												endforeach;
											endif;
											?>"  data-durations="<?php
											if ($destination->durations) :
												foreach ($destination->durations as $duration) :
													echo "-". $duration ."-";
												endforeach;
											endif;
											?>" data-camps="<?php
											if ($destination->get_camps()) :
												foreach ($destination->get_camps() as $camp) :
													echo "-". $camp->booking_code ."-";
												endforeach;

											endif;
											?>"><?php echo $destination->title; ?></option>
											<?php
										endif;
									endforeach;
									?>
								</select>
							</div>

							<div class="book-choice-container">
								<select class="select book-camp book-choice">
									<option value=""><?php echo __("Select camp", "lapoint"); ?></option>
									<?php
									$added_camps = array();
									foreach ($camps as $camp) :
										if ($camp->booking_code && !in_array($camp->booking_code, $added_camps)) :
											$added_camps[] = $camp->booking_code;

											$booking_title = $camp->title;
											if ($camp->booking_label) :
												$booking_title = $camp->booking_label;
											endif; ?>
											<option value="<?php echo $camp->id; ?>" data-destination="<?php echo $camp->get_destination()->id; ?>" data-destination-type="<?php echo $camp->get_type()->id; ?>" data-code="<?php echo $camp->booking_code; ?>"><?php echo $booking_title; ?></option>
											<?php
										endif;
									endforeach;
									?>
								</select>
							</div>

							<div class="book-choice-container">
								<?php
								$level_parents = array();
								foreach ($levels as $level) :
									if ($level->parent_level) :
										if (!in_array($level->parent_level->ID, $level_parents)) :
											$level_parents[] = $level->parent_level->ID;
										endif;
									endif;
								endforeach;
								?>
								<select class="select book-level book-choice">
									<option value=""><?php echo __("Select level", "lapoint"); ?></option>
									<?php
									foreach ($levels as $level) :
										if ($level->booking_code && !in_array($level->id, $level_parents)) : ?>
											<option value="<?php echo $level->id; ?>" data-destination-type="<?php echo $level->get_type()->id; ?>" data-code="<?php echo $level->booking_code; ?>"><?php echo $level->display_label; ?></option>
											<?php
										endif;
									endforeach;
									?>
								</select>
							</div>

							<div class="book-choice-container">
								<select class="select book-duration book-choice">
								<option value=""><?php _e("Duration", "lapoint"); ?></option>
								<option class="option" value="WE"><?php _e("Weekend", "lapoint"); ?></option>
								<option class="option" value="1"><?php _e("1 day", "lapoint"); ?></option>
								<option class="option" value="2"><?php _e("2 days", "lapoint"); ?></option>
								<option class="option" value="3"><?php _e("3 days", "lapoint"); ?></option>
								<option class="option" value="4"><?php _e("4 days", "lapoint"); ?></option>
								<option class="option" value="5"><?php _e("5 days", "lapoint"); ?></option>
								<option class="option" value="6"><?php _e("6 days", "lapoint"); ?></option>

								<option class="option" value="7"><?php _e("1 week", "lapoint"); ?></option>
								<option class="option" value="14"><?php _e("2 weeks", "lapoint"); ?></option>
								<option class="option" value="21"><?php _e("3 weeks", "lapoint"); ?></option>
								</select>
							</div>

							<div class="book-choice-container">
								<input class="book-start-date book-choice" type="text" placeholder="<?php echo __("Start date", "lapoint"); ?>">
							</div>

							<div class="book-choice-container">
								<button type="button" class="btn btn-show btn-inverted pull-right"><?php echo __("Search", "lapoint"); ?></button>
							</div>
						</div>

						<div class="result-container"></div>
						<div class="loader">
							<div class="spinner">
								<div class="bounce1"></div>
								<div class="bounce2"></div>
								<div class="bounce3"></div>
							</div>
						</div>

						<div class='pagination'>
			
							<div class='pagination-nav'>
								<div class='prev'>
									<a class='prev-link' href='javascript:void(0);'> << </a>
								</div>

								<div class='end-of-results later'>
									<span>For later departues change the start date and seach again.</span>
								</div>
								<div class='end-of-results earlier'>
									<span>For earlier departues change the start date and seach again.</span>
								</div>
								
								<div class='next'>
									<a class='next-link' href='javascript:void(0);'> >> </a>
								</div>
							</div>
							
						</div>

					</div>

			</div>


			<div class="container">
				<div class="secondary-menu">
					<?php
					$languages = icl_get_languages('skip_missing=0&orderby=code');
				    if (!empty($languages)) : ?>
						<div class="select-language pull-right">
							<a href="#">
								<span class="label"><?php echo __("Select language", "lapoint"); ?></span>
								<span class="code"><?php echo ICL_LANGUAGE_CODE; ?></span>
							</a>
							<div class="dropdown">
								<ul class="mas">
									<?php
									foreach ($languages as $l) :
										if ($l['active']) : ?>
											<li class="active">
												<?php echo $l['native_name']; ?>
											</li>
										<?php else : ?>
											<li>
												<a href="<?php echo $l['url']; ?>">
													<?php echo $l['native_name']; ?>
												</a>
											</li>
										<?php endif;
									endforeach;
									?>
								</ul>
							</div>
						</div>
					    <?php
					endif; ?>

					<div class="pull-right">
						<?php wp_nav_menu(array('theme_location' => 'secondary')); ?>
					</div>
				</div>
			</div>
		</nav>

		<button type="button" role="button" aria-label="Visa menyn" class="menu-icon lines-button x2">
			<span class="lines"></span>
		</button>
	</header>

	<main id="main" role="main">

			<?php
/*
			$host = home_url();
			$pages = get_posts(array(
				'posts_per_page' => -1,
				'post_type' => 'page',
				'orderby' => 'title',
				'order' => 'ASC',
				'post_status' => 'publish',
				'suppress_filters' => 1
			));

			echo "Page:<br>";
			$count = 0;
			foreach ($pages as $page) {
				echo $page->ID ." - ";
				$page_language_details = apply_filters('wpml_post_language_details', NULL, $page->ID);
				$code = $page_language_details["language_code"];
				$link = home_url(get_page_uri($page->ID));
				if ($code != "en") {
					$link = str_replace($host, $host . "/" . $code, $link);
				}
				echo $page->post_title ." (". $code ."):<br> <a href='". $link ."' target='_blank'>". $link ."</a><br>";
				$count++;
			}
			echo "Count: " . $count ."<br>";

			*/