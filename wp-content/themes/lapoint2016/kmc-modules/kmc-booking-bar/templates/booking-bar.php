<?php
global $destination_types_manager, $destinations_manager, $camps_manager, $levels_manager;
$destination_types = $destination_types_manager->get_all();
$destinations = $destinations_manager->get_all();
$camps = $camps_manager->get_all();
$levels = $levels_manager->get_all();
$default_level_set = false;

$default_duration = "7";

// we pick default search duration from default_camp if there is one otherwise from default_destination and fallback to 1 week
if( $this->default_camp ) {
	foreach ($camps as $camp) {
		if ($this->default_camp == $camp->id) {
			$default_duration = $camp->search_duration ? $camp->search_duration : "7";
		}
	}
} else if( $this->default_destination ) {
	foreach ($destinations as $destination) {
		if ($this->default_destination == $destination->id) {
			$default_duration = $destination->search_duration ? $destination->search_duration : "7";
		}
	}
}

?>
	
	<?php if ($this->post->post_title) : ?>
		<h2 class="center"><?php
		if ($this->post->post_title == "DEFAULT") :
			echo __("Search and book your trip", "lapoint");
		else:
			echo $this->post->post_title;
		endif;
		?></h2>
	<?php endif; ?>

	<?php if ($this->show_description) : ?>
		<p class="center description"><?php echo __("The first available start date after the date you selected is displayed below.", "lapoint"); ?></p>
	<?php endif; ?>

	<div class="kmc-booking-bar booking-bar container clearfix" data-animated="true" data-auto-search="<?php echo $this->auto_search; ?>" data-book-label="<?php _e('Book', 'lapoint'); ?>">
		<div class="row">
			
			<div class="book-choice-container destination-type">
				<select class="select book-destination-type book-choice">
					<?php
					foreach ($destination_types as $destination_type) :
						if ($destination_type->booking_code) : ?>
							<option value="<?php echo $destination_type->id; ?>"<?php
							if ($this->default_destination_type == $destination_type->id) echo ' selected="selected"';
							?> data-code="<?php echo $destination_type->booking_code; ?>"><?php echo $destination_type->title; ?></option>
							<?php
						endif;
					endforeach;
					?>
				</select>
			</div>

			<div class="book-choice-container destination">
				<select class="select book-destination book-choice">
					<option value=""><?php echo __("Select destination", "lapoint"); ?></option>
					<?php
					foreach ($destinations as $destination) :
						if ($destination->booking_code) : ?>
							<option value="<?php echo $destination->id; ?>" 
								data-code="<?php echo $destination->booking_code; ?>" 
								data-destination-type="<?php echo $destination->get_type()->id; ?>"
								<?php if ($this->default_destination == $destination->id) echo ' selected="selected"'; ?> 
								data-levels="<?php if ($destination->levels) :
									foreach ($destination->levels as $level) :
										echo "-". $level->ID ."-";
									endforeach;
								endif;?>"  
								data-durations="<?php if ($destination->durations) :
									foreach ($destination->durations as $duration) :
										echo "-". $duration ."-";
									endforeach;
								endif;?>" 
								data-camps="<?php if ($destination->get_camps()) :
									foreach ($destination->get_camps() as $camp) :
										echo "-". $camp->booking_code ."-";
									endforeach;
								endif;?>"
								data-search-duration="<?php echo $destination->search_duration ? $destination->search_duration : 7; ?>"><?php echo $destination->title; ?></option>
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
						// Guessing that it is for camps to have the same booking code. Currently a camp is mapped to ONE destination. So might have to create duplicate camps
						// for different destination types. 				
						if ($camp->booking_code ) : //&& !in_array($camp->booking_code, $added_camps)) :
							//$added_camps[] = $camp->booking_code;

							$booking_title = $camp->title;
							if ($camp->booking_label) :
								$booking_title = $camp->booking_label;
							endif; ?>

							<option value="<?php echo $camp->id; ?>" 
								data-destination="<?php echo $camp->get_destination()->id; ?>" 
								data-destination-type="<?php echo $camp->get_type()->id; ?>" 
								data-code="<?php echo $camp->booking_code; ?>"								
								data-levels="<?php if ($camp->levels) {
															foreach ($camp->levels as $level) {
																echo "-". $level->ID ."-";
															}
														} ?>"
								data-search-duration="<?php echo $camp->search_duration ? $camp->search_duration : 7; ?>"
								<?php if ($this->default_camp == $camp->id) echo ' selected="selected"'; ?>><?php echo $booking_title; ?></option>
							<?php

						endif;
					endforeach;
					?>
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
							<option value="<?php echo $level->id; ?>" 
								data-destination-type="<?php echo $level->get_type()->id; ?>" 
								data-code="<?php echo $level->booking_code; ?>"
								data-constraint="<?php echo $level->constraint == true ? 'match' : 'none'; ?>"
								<?php if ($this->default_level == $level->id) { echo ' selected="selected"'; $default_level_set = true; }; ?>>
								<?php echo $level->display_label; ?></option>								
							<?php
						endif;
					endforeach;
					?>
				</select>
			</div>

			<div class="book-choice-container duration">
				<select class="select book-duration book-choice">
					<option value=""><?php _e("Select duration", "lapoint"); ?></option>
					<option class="option" value="WE" <?php echo $default_duration == "WE" ? "selected='selected'" : ""; ?>><?php _e("Weekend", "lapoint"); ?></option>
					<option class="option" value="1" <?php echo $default_duration == "1" ? "selected='selected'" : ""; ?>><?php _e("1 day", "lapoint"); ?></option>
					<option class="option" value="2" <?php echo $default_duration == "2" ? "selected='selected'" : ""; ?>><?php _e("2 days", "lapoint"); ?></option>
					<option class="option" value="3" <?php echo $default_duration == "3" ? "selected='selected'" : ""; ?>><?php _e("3 days", "lapoint"); ?></option>
					<option class="option" value="4" <?php echo $default_duration == "4" ? "selected='selected'" : ""; ?>><?php _e("4 days", "lapoint"); ?></option>
					<option class="option" value="5" <?php echo $default_duration == "5" ? "selected='selected'" : ""; ?>><?php _e("5 days", "lapoint"); ?></option>
					<option class="option" value="6" <?php echo $default_duration == "6" ? "selected='selected'" : ""; ?>><?php _e("6 days", "lapoint"); ?></option>
					<!-- selected="selected" -->					
					<option class="option" value="7" <?php echo $default_duration == "7" ? "selected='selected'" : ""; ?>><?php _e("1 week", "lapoint"); ?></option>
					<option class="option" value="14" <?php echo $default_duration == "14" ? "selected='selected'" : ""; ?>><?php _e("2 weeks", "lapoint"); ?></option>
					<option class="option" value="21" <?php echo $default_duration == "21" ? "selected='selected'" : ""; ?>><?php _e("3 weeks", "lapoint"); ?></option>
				</select>
			</div>

			<div class="book-choice-container start-date">				
				<input class="book-start-date book-choice" type="text" placeholder="<?php echo __("Start date", "lapoint"); ?>">
			</div>

			<div class="book-choice-container search">
				<button type="button" class="btn btn-show btn-inverted pull-right disabled"><?php echo __("Search", "lapoint"); ?></button>
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


<?php
/*
<div book_menu_string="Book" lang="uk" id="destination_chooser">

	<div class="destination_inner">
		<div class="quatro quatro-destination">
					                                <label>Start date</label>
                        					<input type="text" class="choose-date form-control hasDatepicker" placeholder="Startdatum" value="07/03/2016" id="dp1457355586288">
		</div>
		<div class="quatro quatro-destination">
					                                <label>Destination</label>
                        					<select class="choose-destination form-control" name="destination">
				<option value="" selected="" disabled="">Destination</option>
										<option value="Kitecruise Egypt">Kitecruise Egypt</option>
									<option value="Kite camp Italia Sardinia">Kite camp Italia Sardinia</option>
									<option value="Kite camp Portugal Esposende">Kite camp Portugal Esposende</option>
									<option value="Surf camp Australia Noosa">Surf camp Australia Noosa</option>
									<option value="Surf camp Costa Rica Santa Teresa">Surf camp Costa Rica Santa Teresa</option>
									<option value="Surf camp Bali Canggu">Surf camp Bali Canggu</option>
									<option value="Surf camp Morocco Taghazout">Surf camp Morocco Taghazout</option>
									<option value="Surf camp Norway Stadt">Surf camp Norway Stadt</option>
									<option value="Surf camp Portugal Ericeira">Surf camp Portugal Ericeira</option>
									<option value="Surf camp Sri Lanka">Surf camp Sri Lanka</option>
								</select>
		</div>

		<div class="quatro quatro-packet">
			 		                                <label style="">Package</label>
                        					<select style="" class="choose-paket form-control placeholder" disabled="" name="paket">
			</select>
		</div>

		<div class="quatro quatro-packet">
					                                <label style="">Duration</label>
                        					<select style="" class="choose-length form-control placeholder" disabled="" name="length">
				<option value="" static="" selected="">All</option>
			</select>
		</div>

		<div class="close" style="cursor: pointer">
			<img src="/wp-content/plugins/agl-button/images/unnamed.png">
		</div>

		<div style="clear: both"></div>

		<div class="spinner" style="display: none">
			<center>
			<br><br>
			<img src="/wp-content/themes/lapoint/img/loading-spinner.gif">
			</center>
		</div>
		<div id="destination_result"></div>
</div>
</div>

*/
?>