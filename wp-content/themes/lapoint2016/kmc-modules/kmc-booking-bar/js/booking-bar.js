(function ($) {

	$(function () {
		var BookingBar = Backbone.View.extend({
			initialize: function () {
				var _this = this;
				this.$destination_type = this.$(".book-destination-type");
				this.$destination = this.$(".book-destination");
				this.$camp = this.$(".book-camp");
				this.$level = this.$(".book-level");
				this.$duration = this.$(".book-duration");
				this.$start_date = this.$(".book-start-date");
				this.$result_container = this.$(".result-container");

				this.$pagination = this.$(".pagination");
				this.paginationIndex = 0;
				this.totalPages = 1;
				this.outputTables = Array();

				// set to true when the destination filter is clicked and set to false when anything else is clicked
				this.last_destination = false;

				// so you can't search on the same date unless somethings has been updated
				this.lastSearch = false;

				// so we know when someone has explicitly set a duration
				this.explicitDuration = false;

				this.scrollOffsetTop = 0;
				this.closeBookingFrameButton = $("<div class='close-booking-frame'><button class='lines-button x2' type='button'><span class='lines'></span></button></div>");

				var date_format = "yy-mm-dd";
				switch (lapoint.language) {
					case "sv":
						date_format = "yy-mm-dd";
						break;
					case "da":
						date_format = "dd-mm-yy";
						break;
					case "nb":
						date_format = "dd.mm.yy";
						break;
					case "en":
						date_format = "dd/mm/yy";
						break;
				}

				this.$start_date.datepicker({
					dateFormat: date_format
				}).on('changeDate', function () {
						$(this).blur();
				}).on('focus', function(){
					$(this).trigger('blur');
				});

				this.update_destinations(false, false);
				this.update_levels(false);
				this.update_camps(false);

				if (this.$el.attr("data-auto-search") == "1") {
					this.$el.data("in-view-callback", function () {
						_this.on_show_click();
					});
				}

				this.$('.btn-show').removeClass('disabled');
			},
			events: {
				"change .book-destination-type": "on_destination_type_changed",
				"change .book-destination": "on_destination_changed",
				"change .book-camp": "on_camp_changed",
				"change .book-level": "on_level_changed",
				"change .book-duration": "on_duration_changed",
				"click .btn-show": "on_show_click",
				"click .btn-book": "on_book_click"
			},

			on_destination_type_changed: function (e) {
				this.update_destinations(true, true);
				this.update_levels(true);
				this.explicitDuration = false;
			},

			on_destination_changed: function (e) {
				this.update_camps(true);
				this.update_levels(true);
				this.update_durations(true, false);
				this.explicitDuration = false;
			},

			on_level_changed: function (e) {
				this.update_destinations(true, false);
				this.explicitDuration = false;
			},

			on_camp_changed: function (e) {
				this.update_destinations(true, false);
				this.update_levels(true);
				this.explicitDuration = false;
			},

			on_duration_changed: function (e) {				
				this.explicitDuration = true;
			},

			update_destinations: function (set_index, set_fresh) {

				var destination_type = this.$destination_type.val();
				var level = this.$level.val();
				var camp = this.$camp.val();

				// if we are called from the destination_type dropdown
				if( set_fresh ) {
					level = "";
					camp = "";
					this.$destination[0].selectedIndex = 0;
					this.$camp[0].selectedIndex = 0;
					this.$level[0].selectedIndex = 0;
					this.$duration[0].selectedIndex = 0;
				}
				
				this.$destination.find("option[data-destination-type]").attr('disabled','disabled');

				var select = "option";

				if (destination_type) {
					select = "[data-destination-type='" + destination_type + "']";
				}

				if (level) {
					select += "[data-levels*='-" + level + "-']";
				}

				// If we do this then you can't change destination in the dropdown
				/*
				if (camp) {
					var camp_code = this.$camp.find("option:selected").attr("data-code");
					select += "[data-camps*='-" + camp_code + "-']";
				}
				*/

				this.$destination.find(select).removeAttr("disabled");

				if (set_index) {
					if (this.$destination.find("option:selected").is(":disabled")) {
						this.$destination[0].selectedIndex = 0;
					}

					if( this.$destination.find(select).length == 1 ) {
						this.$destination[0].selectedIndex = this.$destination.find(select)[0].index;
					}

				}
				this.$destination.select2("destroy").select2({
					minimumResultsForSearch: Infinity
				});

				this.update_camps(set_index);
				this.update_durations(set_index, set_fresh);

			},

			update_levels: function (set_index) {


				var _this = this;
				var destination_type = this.$destination_type.val();
				var destination = this.$destination.val();
				var camp = this.$camp.val();

				// first disable all levels				
				this.$level.find("option[data-destination-type]").attr('disabled','disabled');
				
				// then we figure out which levels we should show in the drop-down

				// if both a destination and camp is selected
				if( destination && camp ) {

					var levels = this.$camp.find( "option[value='" + camp + "']" ).attr("data-levels");

					// we first check if the camp has levels assigned to it
					if( levels ) {
						levels = levels.substr(1, levels.length-2).split("--");
					} 
					// the camp does not have levels. use levels in the destination
					else {
						levels = this.$destination.find( "option[value='" + destination + "']" ).attr("data-levels");
						levels = levels.substr(1, levels.length-2).split("--");
					}

					// update selectable levels
					_.each(levels, function (level_id) {
						_this.$level.find("option[value='" + level_id + "']").removeAttr("disabled");
					});

				}

				// destination is selected but not a camp. show levels that are set for the destination
				else if (destination) {

					var levels = this.$destination.find("option:selected").attr("data-levels");
					levels = levels.substr(1, levels.length-2).split("--");
					_.each(levels, function (level_id) {
						_this.$level.find("option[value='" + level_id + "']").removeAttr("disabled");
					});

				} 

				// camp is selected but not a destination. 
				else if (camp) {
					
					var levels = this.$camp.find( "option[value='" + camp + "']" ).attr("data-levels");

					// we first check if the camp has levels assigned to it
					if( levels ) {
						levels = levels.substr(1, levels.length-2).split("--");
					} 

					// if not then find the destination the camp belongs to and use its levels 
					else {
						var destination_id = this.$camp.find("option[value='" + camp + "']").attr( "data-destination" );
						levels = this.$destination.find( "option[value='" + destination_id + "']" ).attr("data-levels");
						levels = levels.substr(1, levels.length-2).split("--");
					}

					// update selectable levels
					_.each(levels, function (level_id) {
						_this.$level.find("option[value='" + level_id + "']").removeAttr("disabled");
					});

				} 


				else if (destination_type) {
					// Only destination type is selected. Activate all levels for the destination type that does not have a constraint
					this.$level.find("option[data-destination-type='" + destination_type + "'][data-constraint='none']").removeAttr("disabled");

				}

				if (set_index) {
					if (this.$level.find("option:selected").is(":disabled")) {
						this.$level[0].selectedIndex = 0;
					}
				}

				this.$level.select2("destroy").select2({
					minimumResultsForSearch: Infinity
				});

			},

			update_camps: function (set_index) {
				var _this = this;
				var destination = this.$destination.val();
				var level = this.$level.val();
							
				if (destination) {
					this.$camp.find("option[data-destination]").attr('disabled','disabled');
					this.$camp.find("option[data-destination='" + destination + "']").removeAttr("disabled");
					this.last_destination = destination;

				} else if (level) {
					this.$camp.find("option[data-destination]").attr('disabled','disabled');
					// Show camps for all destinations not disabled
					this.$destination.find("option:not(:disabled)").each(function(index, dest_option) {
						var $dest_option = $(dest_option);
						if ($dest_option.attr("data-camps")) {
							var camps = $(dest_option).attr("data-camps");
							camps = camps.substr(1, camps.length-2).split("--");
							_.each(camps, function (camp_code) {
								_this.$camp.find("option[data-code='" + camp_code + "']").removeAttr("disabled");
							});
						}
					});

				} else {					
					var destination_type = this.$destination_type.val();
					this.$camp.find("option[data-destination-type]").attr('disabled','disabled');
					this.$camp.find("option[data-destination-type='" + destination_type + "']").removeAttr("disabled");
				}

				if( !destination && this.last_destination ) {
					this.$camp[0].selectedIndex = 0;
					this.last_destination = false;
				}

				if (set_index) {
					if (this.$camp.find("option:selected").is(":disabled")) {
						this.$camp[0].selectedIndex = 0;
					}

					if( destination ) {
						if( this.$camp.find("option[data-destination='" + destination + "']").length == 1 ) {
							this.$camp[0].selectedIndex = this.$camp.find("option[data-destination='" + destination + "']")[0].index;	
						}
					}
				}

				this.$camp.select2("destroy").select2({
					minimumResultsForSearch: Infinity
				});

			},

			update_durations: function (set_index, set_fresh) {

				var _this = this;
				var destination = this.$destination.val();
				var level = this.$level.val();
				var camp_id = this.$camp.val();
				var destination_id = false;

				if( camp_id && !destination ) {
					destination_id = this.$camp.find("option[value='" + camp_id + "']").attr( "data-destination" );
				}

				if (destination && level) {
					$.get(ajaxlapoint.ajaxurl, {
						action: 'fetch_packages',
						postNonce: ajaxlapoint.postNonce,
						destination: destination,
						level: level
					}, function (response) {

						if (response && response.status == 200){
							if (response.package.durations) {
								_this.update_durations_for_list(response.package.durations, set_index)
							} else {
								_this.update_durations_for_destination(set_index);
							}
						} else {
							console.log("Error fetching packages...", response);
						}

					});
				} else if (destination) {
					this.update_durations_for_destination(set_index);
				} else if ( destination_id ) {
					this.update_durations_by_destination_id( set_index, destination_id );
				} else {
					
					if( set_fresh ) {
						this.$duration.find("option.option").removeAttr("disabled");
						this.$duration[0].selectedIndex = 0;
						this.explicitDuration = false;						
						this.$duration.select2("destroy").select2({
							minimumResultsForSearch: Infinity
						});
					} else if ( set_index ) {
						if (this.$duration.find("option:selected").is(":disabled")) {
							this.$duration[0].selectedIndex = 0;
							this.$duration.select2("destroy").select2({
								minimumResultsForSearch: Infinity
							});
						}
					}
				}
			},

			update_durations_for_list: function (durations, set_index) {
				var _this = this;
				this.$duration.find("option.option").attr("disabled", "disabled");

				_.each(durations, function (duration_id) {
					_this.$duration.find("option[value='" + duration_id + "']").removeAttr("disabled");
				});

				if (set_index) {
					if (this.$duration.find("option:selected").is(":disabled")) {
						this.$duration[0].selectedIndex = 0;
						this.explicitDuration = false;
					}
				}
				this.$duration.select2("destroy").select2({
					minimumResultsForSearch: Infinity
				});
			},

			update_durations_for_destination: function (set_index) {
				var _this = this;
				this.$duration.find("option.option").attr("disabled", "disabled");

				var durations = this.$destination.find("option:selected").attr("data-durations");
				durations = durations.substr(1, durations.length-2).split("--");

				_.each(durations, function (duration_id) {
					_this.$duration.find("option[value='" + duration_id + "']").removeAttr("disabled");
				});

				if (set_index) {
					if (this.$duration.find("option:selected").is(":disabled")) {
						this.$duration[0].selectedIndex = 0;
						this.explicitDuration = false;
					}
				}
				this.$duration.select2("destroy").select2({
					minimumResultsForSearch: Infinity
				});
			},

			update_durations_by_destination_id: function( set_index, destination_id ) {
				var _this = this;
				var dest = this.$destination.find( "option[value='" + destination_id + "']" );
				
				if( !dest.length ) {
					// things are not configured right in the CMS if there is no destination for the camp. So just return
					return;
				}

				this.$duration.find("option.option").attr("disabled", "disabled"); // reset all
				
				var durations = dest.attr("data-durations");

				durations = durations.substr(1, durations.length-2).split("--");

				_.each(durations, function (duration_id) {
					_this.$duration.find("option[value='" + duration_id + "']").removeAttr("disabled");
				});

				if (set_index) {
					if (this.$duration.find("option:selected").is(":disabled")) {
						this.$duration[0].selectedIndex = 0;
						this.explicitDuration = false;
					}
				}
				this.$duration.select2("destroy").select2({
					minimumResultsForSearch: Infinity
				});
			},

			close_booking_frame: function( _this ) {							

				$(".close-booking-frame").remove();
				$("#travelize-booking-frame").remove();

				// Unhide all sections except the first (if it has an background image)
				$('.kmc-sections .kmc-section').each(function (index, elem) {
					var $el = $(elem);
					if (index > 0 || !($el.hasClass('has-bgr-img') || $el.hasClass('has-bgr-video'))) {
						$el.fadeIn();
					}
				});

				// scroll back last recorded position
				$(document).scrollTop( _this.scrollOffsetTop );	
			},

			on_book_click: function( e ) {
				var _this = this;
				e.preventDefault();

				// close any open booking frame before opening a new one. if using the booking module at the bottom of the page pressing book, then scrolling all the way
				// up and do a search from the main menu (prices) then pressing book we have two frames..
				if( $("#travelize-booking-frame") ) {
					$(".close-booking-frame").remove();
					$("#travelize-booking-frame").remove();
				}
				
				if( $(e.target).hasClass( "disabled" ) ) {
					return;					
				}
				var $tr = $(e.currentTarget).closest("tr");
				if ($tr.attr("data-link")) {
					var url = $tr.attr("data-link");
					//if (window.lapoint.language !== 'sv' && window.lapoint.gaClientId) {
					if (window.lapoint.gaClientId) {
						url += '&clientId=' + window.lapoint.gaClientId
					}

					// save scrolltop so we can get back
					_this.scrollOffsetTop = $(window).scrollTop();

					// Hide all sections except the first (if it has an background image)
					$('.kmc-sections .kmc-section').each(function (index, elem) {
						var $el = $(elem);
						if (index > 0 || !($el.hasClass('has-bgr-img') || $el.hasClass('has-bgr-video'))) {
							$el.fadeOut();
						}
					});

					// Add iframe
					var $iFrame = $('<iframe src="' + url + '" height="800" width="100%" frameborder="0" id="travelize-booking-frame"></iframe>');
					$iFrame.load(function(){
						// jump to top of iframe
						var iFrameTop = $("#travelize-booking-frame").offset().top;
						$(document).scrollTop( iFrameTop );
						$('#main').append( _this.closeBookingFrameButton );
						_this.closeBookingFrameButton.css({
							"position": "absolute",
							"top": (iFrameTop + 25) + "px",
							"right": "25px"
						});
						_this.closeBookingFrameButton.click( function() {							
							_this.close_booking_frame( _this );
						});
					});
					
					$('#main').append($iFrame);
					$iFrame.width($(window).width());					
					$iFrame.hide().fadeIn();
					$iFrame.iFrameResize({
						log: false,
						checkOrigin: false
					});

					$(window).on('resize', function(e) {
						$iFrame.width($(window).width());
						_this.closeBookingFrameButton.css({
							"top": ($("#travelize-booking-frame").offset().top + 25) + "px"
						});
					});

					// Close menu book slider if open
					var $main_booking = $(".main-booking");
					if ($main_booking.hasClass("open")) {
						$(".drop-wrapper").parent().removeClass("open");
						$main_booking.removeClass("open");
						$main_booking.slideUp();
					}					

				}
			},

			on_show_click: function () {
				
				var _this = this;

				// if no date select set to today
				if( !this.$start_date.val() ) {
					this.$start_date.datepicker("setDate", new Date());						
				} 

				// Logic for auto selecting duration
				/*
					Action: No active selection is made before clicking Search.
					Result: Duration is set to 1 week

					Action: Only Destination Type (Select travel type) is selected before clicking Search
					Result: Duration is set to 1 week

					Action: Destination Type & Destination is selected (If the Destination only has ONE camp it is automatically selected). Search.
					Result: Duration is set to 1 week
					Special case: If the destination is Norway (camp is not auto selected) duration is set to 3 days

					Action: Destination Type & Camp is selected. Search.
					Action: Destination Type & Destination & Camp is selected. Search.
					Result: Duration is set to 1 week
					Special case: If the Camp is Hoddevik duration is set to 3 days

					Action: Destination Type & Level is selected. Search.
					Action: Destination Type & Destination & Level is selected. Search.
					Action: Destination Type & Camp & Level is selected. Search.
					Action: Destination Type & Destination & Camp & Level is selected. Search.
					Result: Duration is not set
				*/

				var destination_type = this.$destination_type.val();
				var destination = this.$destination.val();
				var camp = this.$camp.val();
				var level = this.$level.val();
				var duration = this.$duration.val();
				var searchDuration = false;				

				// run auto logic unless duration has been explicitly set or a level is selected

				if( !this.explicitDuration && !level ) {

					if( camp ) {
						searchDuration = this.$camp.find("option:selected").attr("data-search-duration");
					} else if( destination ) {
						searchDuration = this.$destination.find("option:selected").attr("data-search-duration");
					} else {
						searchDuration = 7;
					}

					if( searchDuration ) {
						var durationFilter = 'option[value=' + searchDuration + ']';

						this.$duration.find( durationFilter ).attr("selected", true);
						this.$duration[0].selectedIndex = this.$duration.find( durationFilter )[0].index;
						this.$duration.select2("destroy").select2({
							minimumResultsForSearch: Infinity
						});	
					}

				}
				
			
				var lang = lapoint.country.substr(-2);
				if (lang == "US") {
					lang = "UK";
				}

				var data = {
					destination_type: this.$destination_type.find("option:selected").attr("data-code"),
					destination: this.$destination.find("option:selected").attr("data-code"),
					camp: this.$camp.find("option:selected").attr("data-code"),
					level: this.$level.find("option:selected").attr("data-code"),
					duration: this.$duration.val(),
					startDate: this.$start_date.val(),
					lang: lang,
					maxnumberfortourlist1: 60
				}

				// don't allow exact same search twice
				if( this.lastSearch ) {
					if (JSON.stringify( this.lastSearch ) ==  JSON.stringify( data ) ) {
						return;
					}
				}

				var lbl_book = this.$el.closest(".kmc-booking-bar");
				this.$el.addClass("open").addClass("loading");
				this.$el.removeClass("has-results no-more-later first last");
				this.$el.find(".table-wrapper").remove();

				setTimeout(function () {
					_this.$(".loader").addClass("active");
				}, 200);


				this.load_travelize_data_2( data );

				// remember and don't allow same exact search twice
				this.lastSearch = data;

			},

			load_travelize_data_2: function ( data ) {

				var _this = this;
				_this.paginationIndex = 0;
				_this.paginationIndex = 0;
				_this.totalPages = 1;
				_this.outputTables = Array();

				// load travelize html data into unattached element
				$travelize_data = $("<div class='table-wrapper'></div>");

				$travelize_data.load(lapoint.travelize_wrapper + "?" + jQuery.param(data), function() {

					_this.parse_travelize_data( $travelize_data ); 

					$travelize_data.find( "table" ).replaceWith( _this.outputTables[ _this.paginationIndex ].table );
					
					_this.$result_container.fadeOut( 300, function() {
						_this.$result_container.html( $travelize_data );
						_this.$el.removeClass("loading");
					
						_this.$el.addClass("has-results first");
						_this.$result_container.fadeIn( 420 );
					});

					$next_link = _this.$el.find(".pagination .next");
					$prev_link = _this.$el.find(".pagination .prev");

					$next_link.click( function() {

						_this.paginationIndex++;

						_this.$el.removeClass( 'no-more-later no-more-earlier first last' );

						if( _this.paginationIndex >= _this.totalPages ) {
							_this.paginationIndex = _this.totalPages;
							_this.$el.addClass( 'no-more-later last' );
						}
						$travelize_data.find( "table" ).replaceWith( _this.outputTables[ _this.paginationIndex ].table );


					});

					$prev_link.click( function() {

						_this.paginationIndex--;

						_this.$el.removeClass( 'no-more-later no-more-earlier first last' );

						if( _this.paginationIndex <= 0 ) {
							_this.paginationIndex = 0;
							_this.$el.addClass( 'no-more-earlier first' );
						}

						$travelize_data.find( "table" ).replaceWith( _this.outputTables[ _this.paginationIndex ].table );


					});

				});

			},	

			
			parse_travelize_data: function( $travelize_data )	{

				var spots_left = {
					"en" : {
						0 : "No spots left",
						1 : "1 spot left",
						2 : "2 spots left",
						3 : "3 spots left",
						4 : "4 spots left"
					},
					"sv" : {
						0 : "Inga platser kvar",
						1 : "1 plats kvar",
						2 : "2 platser kvar",
						3 : "3 platser kvar",
						4 : "4 platser kvar"
					},
					"nb" : {
						0 : "Ingen plasser igjen",
						1 : "1 plass igjen",
						2 : "2 plasser igjen",
						3 : "3 plasser igjen",
						4 : "4 plasser igjen"
					},
					"da" : {
						0 : "Ingen pladser igen",
						1 : "1 plads igen",
						2 : "2 pladser igen",
						3 : "3 pladser igen",
						4 : "4 pladser igen"
					}
				}
				
				// remove 
				$travelize_data.find(".tableselector_header").remove();

				// fix table header
				if (!$travelize_data.find(".colNoAvailableOffers").size()) {
					$travelize_data.find(".tableheader").append( $("<td></td>").css("width", "130px") );
				}

				var totalRowsInTable = $travelize_data.find( "tr" ).size();
				
				// go trough each row. 
				// remove any GROUP from the result
				// and do some general housekeeping so the rows are in a format we want
				$travelize_data.find("tr.row").each( function(index, el) {
					$row = $(el);
					
					// remove fully booked results
					$availabilitySpan = $row.find(".colAvailability span");
					var available = $availabilitySpan.text();
					var availableInt = parseInt( available, 10 );

					// Fully booked
					if( isNaN(availableInt) && available.substr(0,1) != ">" || availableInt == 0 ) {
						$row.remove();
						return; // skip to next row in iteration
						//$row.addClass("not-available");
						//$row.find(".btn").addClass("disabled");
						//$availabilitySpan.text( spots_left[lapoint.language][0] );
					}

					$colBookPrice = $row.find(".colBookPrice");

					$bookingLinkEl = $colBookPrice.find(".bookingPrice a");

					if( $bookingLinkEl.length > 0 ) {

						var bookingLink = $colBookPrice.find(".bookingPrice a").attr("href");
					
						if( bookingLink.indexOf( "%5FGROUPS%5F" ) > 0 ) {
							//console.log( "removing GROUP from results" );
							$row.remove();
							return; // skip to next row in iteration
						}

						// get the price and remove SEK if it is in the string
						var bookingPrice = $bookingLinkEl.text().replace("SEK ","") + ":-";
						// add booking link as attr to the row
						$row.attr("data-link", bookingLink);
						// remove span.bookingPrice and span.bookingStatus from the col
						$colBookPrice.find("span").remove();
						// add the price and set css
						$colBookPrice.html(bookingPrice).css("text-align","right").css("padding-right","10px");
						// append the book button to the row
						$row.append( $("<td class='colBookButton'></td>").css("text-align", "right").append(
							$("<a href='#'>" + lapoint.loc.book + "</a>").addClass("btn btn-cta btn-primary btn-book")));
					
					} else {

						$bookingPriceEl = $colBookPrice.find(".bookingPrice");
						// get the price and remove SEK if it is in the string
						var bookingPrice = $bookingPriceEl.text().replace("SEK ","") + ":-";
						// remove span.bookingPrice and span.bookingStatus from the col
						$colBookPrice.find("span").remove();
						// add the price and set css
						$colBookPrice.html(bookingPrice).css("text-align","right").css("padding-right","10px");
						// append the book button to the row
						$row.append( $("<td class='colBookButton'></td>").css("text-align", "right").append(
							$("<a href='#'>" + lapoint.loc.book + "</a>").addClass("btn btn-cta btn-primary btn-book")));
					}
						
					// change row classes
					$row.removeClass("row").addClass("row2");										

					// Change output if spots left are less than 5
					if( availableInt <= 4 ) {
						$availabilitySpan.text( spots_left[lapoint.language][availableInt] );
					}

				});

				// reorganize into a new table displaying table header + X results
				var nbrResultRows = 9; // counter starts at zero
				var injectHeader;
				var rowsCounter = 0;								
				var tmpRows = Array();

				$travelize_data.find("tr").each( function(index, el) {

					$row = $(el);

					var currentIsHeader = false;

					if( $row.hasClass( "tableheader" ) ) {
						$injectHeader = $row;
						currentIsHeader = true;
					}

					if( rowsCounter == nbrResultRows ) {
						
						if( !currentIsHeader ) {
							tmpRows.push( $injectHeader.clone() );
							rowsCounter = 0;
						} else {
							rowsCounter = -1;
						}

					}

					tmpRows.push( $row.remove() );

					rowsCounter++;
					if( index == 0 ) {
						rowsCounter--;
					}

				});

				var paginationCounter = 0;
				
				$tableTemplate = $( '<table cellspacing="0" cellpadding="0" class="tourlist tourlist1"></table>' );

				this.outputTables.push( {page: 0, table: $tableTemplate.clone() });
				for( i = 0; i < tmpRows.length; i++ ) {

					if( i != 0 && i % (nbrResultRows+1) == 0 ) {
						paginationCounter++;
						this.outputTables.push( {page: paginationCounter, table: $tableTemplate.clone() });
					}
					
					tmpRows[i].appendTo( this.outputTables[paginationCounter].table );

				}
				
				this.totalPages = paginationCounter;

				$tableTemplate.remove();

			}

		});


		// Setup booking bars on page
		if ($(".kmc-booking-bar").length) {
			$(".kmc-booking-bar").each(function (index, element) {
				var b = new BookingBar({
					el: element
				});
			});
		}

	});

})(jQuery);