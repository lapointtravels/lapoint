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

	<meta name="p:domain_verify" content="fbded96166427723588700f11739771e"/>

	<?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php endif; ?>

	<!-- becuase freshchat misses to add it -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
	
		
	<!--[if lt IE 9]>
		<script src="<?php echo THEME_URL; ?>/js/html5.js"></script>
	<![endif]-->

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
				<div class="row clearfix container">
					<?php					
					foreach ($filtered_destination_types as $destination_type) :
						$destinations = $destination_type->get_destinations();
						?>
						<div class="col col-sm-3">
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
				<div class="row clearfix container">
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
				$default_level_set = false;

				$current_destination_code = get_current_destination_type_booking_code();
				//$current_destination_code = "";

				?>

					<div class="kmc-booking-bar booking-bar container clearfix" data-animated="true">
						<div class="row">
							<div class="book-choice-container destination-type">
								<select class="select book-destination-type book-choice">
									<option value="0"><?php echo __("Select travel type", "lapoint"); ?></option>
									<?php
									foreach ($destination_types as $destination_type) :
										if ($destination_type->booking_code) : ?>
											<option value="<?php echo $destination_type->id; ?>" 
												<?php if ($current_destination_code == $destination_type->booking_code) echo ' selected="selected"'; ?>
												data-code="<?php echo $destination_type->booking_code; ?>"><?php echo $destination_type->title; ?></option>
											<?php
										endif;
									endforeach;
									?>
								</select>
							</div>

							<div class="book-choice-container start-date">
								<input class="book-start-date book-choice" type="text" placeholder="<?php echo __("Start date", "lapoint"); ?>">
							</div>

							<div class="book-choice-container destination">
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

							<div class="book-choice-container camp">
								<select class="select book-camp book-choice">
									<option value=""><?php echo __("Select camp", "lapoint"); ?></option>
									<?php
									$added_camps = array();
									foreach ($camps as $camp) :
										if ($camp->booking_code ) : 
											$booking_title = $camp->title;
											if ($camp->booking_label) :
												$booking_title = $camp->booking_label;
											endif; ?>
											<option value="<?php echo $camp->id; ?>" data-destination="<?php echo $camp->get_destination()->id; ?>" data-destination-type="<?php echo $camp->get_type()->id; ?>" data-code="<?php echo $camp->booking_code; ?>"
												data-levels="<?php if ($camp->levels) :
													foreach ($camp->levels as $level) :
														echo "-". $level->ID ."-";
													endforeach;
												endif; ?>"><?php echo $booking_title; ?></option>
											<?php
										endif;
									endforeach;
									?>
								</select>
							</div>

							<div class="book-choice-container duration">
								<select class="select book-duration book-choice">
								<option value=""><?php _e("Select duration", "lapoint"); ?></option>
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
							
							<div class="book-choice-container level">
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
											<option value="<?php echo $level->id; ?>" data-destination-type="<?php echo $level->get_type()->id; ?>" data-code="<?php echo $level->booking_code; ?>" data-constraint="<?php echo $level->constraint == true ? 'match' : 'none'; ?>"><?php echo $level->display_label; ?></option>
											<?php
										endif;
									endforeach;
									?>
								</select>
							</div>										

							<div class="book-choice-container search">
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

							<?php

								switch ( ICL_LANGUAGE_CODE ) {
									case 'sv':
										$eor_later_string = "För att se senare datum, ändra startdatum och gör en ny sökning";
										$eor_earlier_string = "För att se tidigare datum, ändra startdatum och gör en ny sökning";
										$pagination_next_string = "Senare";
										$pagination_prev_string = "Tidigare";
										break;

									case 'da':
										$eor_later_string = "For senere afgange skal du ændre startdatoen og søge igen";
										$eor_earlier_string = "For tidligere afgange skal du ændre startdatoen og søge igen";
										$pagination_next_string = "Senere";
										$pagination_prev_string = "Tidligere";
										break;

									case 'nb':
										$eor_later_string = "Vennligst endre startdatoen og søk igjen for å se senere avganger";
										$eor_earlier_string = "Vennligst endre startdatoen og søk igjen for å se tidligere avganger";
										$pagination_next_string = "Senere";
										$pagination_prev_string = "Tidligere";
										break;
									
									default:
										$eor_later_string = "For later departures change the start date and seach again.";
										$eor_earlier_string = "For earlier departures change the start date and seach again.";
										$pagination_next_string = "Later";
										$pagination_prev_string = "Earlier";
										break;
								}

							?>
			
							<div class='pagination-nav'>
								<div class='prev'>
									<a class='prev-link' href='javascript:void(0);'> <?php echo $pagination_prev_string; ?> </a>
								</div>

								<div class='end-of-results later'>					
									<span><?php echo $eor_later_string; ?></span>
								</div>
								<div class='end-of-results earlier'>
									<span><?php echo $eor_earlier_string; ?></span>
								</div>
								
								<div class='next'>
									<a class='next-link' href='javascript:void(0);'> <?php echo $pagination_next_string; ?> </a>
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