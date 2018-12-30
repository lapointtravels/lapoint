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

				// so you can't search on the same date
				this.lastSearch = false;

				this.scrollOffsetTop = 0;
				this.closeBookingFrameButton = $("<div class='close-booking-frame'><button class='lines-button x2' type='button'><span class='lines'></span></button></div>");

				var date_format = "yy-mm-dd";
				switch (lapoint.language) {
					case "se":
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


				this.update_destinations(false);
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
				"click .btn-show": "on_show_click",
				"click .btn-book": "on_book_click"
			},

			on_destination_type_changed: function (e) {
				this.update_destinations(true);
				this.update_levels(true);
			},

			on_destination_changed: function (e) {
				this.update_camps(true);
				this.update_levels(true);
				this.update_durations(true);
			},

			on_level_changed: function (e) {
				this.update_destinations(true);
				//this.update_camps(true);
			},

			on_camp_changed: function (e) {
				this.update_destinations(true);
				this.update_levels(true);
			},


			update_destinations: function (set_index) {
				var destination_type = this.$destination_type.val();
				var level = this.$level.val();
				var camp = this.$camp.val();

				
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
				}
				this.$destination.select2("destroy").select2({
					minimumResultsForSearch: Infinity
				});

				this.update_camps(set_index);
				this.update_durations(set_index);
			},

			update_levels: function (set_index) {
				var _this = this;
				var destination_type = this.$destination_type.val();
				var destination = this.$destination.val();
				var camp = this.$camp.val();

				this.$level.find("option[data-destination-type]").attr('disabled','disabled');

				if (destination) {
					var levels = this.$destination.find("option:selected").attr("data-levels");
					levels = levels.substr(1, levels.length-2).split("--");
					_.each(levels, function (level_id) {
						_this.$level.find("option[value='" + level_id + "']").removeAttr("disabled");
					});
				} else if (camp) {
					// Show camps for all destinations not disabled
					this.$destination.find("option:not(:disabled)").each(function(index, dest_option) {
						var $dest_option = $(dest_option);
						if ($dest_option.attr("data-levels")) {
							var levels = $(dest_option).attr("data-levels");
							levels = levels.substr(1, levels.length-2).split("--");
							_.each(levels, function (level_id) {
								_this.$level.find("option[value='" + level_id + "']").removeAttr("disabled");
							});
						}
					});

				} else if (destination_type) {
					this.$level.find("option[data-destination-type='" + destination_type + "']").removeAttr("disabled");
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
				if (set_index) {
					if (this.$camp.find("option:selected").is(":disabled")) {
						this.$camp[0].selectedIndex = 0;
					}
				}
				this.$camp.select2("destroy").select2({
					minimumResultsForSearch: Infinity
				});
			},

			update_durations: function (set_index) {
				var _this = this;
				var destination = this.$destination.val();
				var level = this.$level.val();

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
				}


				if (destination) {
					this.update_durations_for_destination(set_index);
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
					}
						
					// change row classes
					$row.removeClass("row").addClass("row2");
					// check availability
					var available = $row.find(".colAvailability span").text();
					if ((isNaN(available) && available.substr(0, 1) != "<"  && available.substr(0, 1) != ">") || available == "0") {
						$row.addClass("not-available");
						$row.find(".btn").addClass("disabled");
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