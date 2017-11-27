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
				"click .btn-show": "on_show_click"
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
					//this.$destination.find("option:not([data-levels*='-" + level + "-'])").removeAttr("disabled");
				}

				if (camp) {
					var camp_code = this.$camp.find("option:selected").attr("data-code");
					select += "[data-camps*='-" + camp_code + "-']";
				}

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

			on_show_click: function () {
				var _this = this;
				var lbl_book = this.$el.closest(".kmc-booking-bar");
				this.$el.addClass("open").addClass("loading");
				setTimeout(function () {
					_this.$(".loader").addClass("active");
				}, 200);

				var lang = lapoint.country.substr(-2);
				if (lang == "US") {
					lang = "UK";
				}/* else if (lang == "NB") {
					lang = "NO";
				}*/

				var data = {
					destination_type: this.$destination_type.find("option:selected").attr("data-code"),
					destination: this.$destination.find("option:selected").attr("data-code"),
					camp: this.$camp.find("option:selected").attr("data-code"),
					level: this.$level.find("option:selected").attr("data-code"),
					duration: this.$duration.val(),
					startDate: this.$start_date.val(),
					lang: lang,
					maxnumberfortourlist1: 10
				}


				this.$result_container.load(lapoint.travelize_wrapper + "?" + jQuery.param(data), function() {

					_this.$(".tableselector_header").remove();
					_this.$el.removeClass("loading");



					$(".bookingPrice").each(function(){
						var $price = $(this);
						var $a = $price.find("a");
						var $parent = $price.parent();
						$price.remove();

						if ($a.length > 0){
							var price = $a.text().replace("SEK ","") + ":-";
							var $tr =  $parent.closest("tr");

							// Don't show group results
							if ($a.attr("href").indexOf("%5FGROUPS%5F") > 0) {
								$tr.remove()
							} else {
								$tr.attr("data-link", $a.attr("href"));
								$parent.html(price).css("text-align","right").css("padding-right","10px");
							}

						}

					});

					if (!_this.$(".colNoAvailableOffers").size()) {
						_this.$(".tableheader").append(
							$("<td></td>").css("width", "130px")
						);
						_this.$("tr:not(.tableheader)").append(
							$("<td></td>").css("text-align", "right").append(
								$("<a href='#'>" + lapoint.loc.book + "</a>").addClass("btn btn-cta btn-primary btn-book")
							)
						);

						_this.$(".btn-book:not(.disabled)").on("click", function (e) {
							e.preventDefault();
							var $tr = $(e.currentTarget).closest("tr");
							if ($tr.attr("data-link")) {
								var url = $tr.attr("data-link");
								//if (window.lapoint.language !== 'sv' && window.lapoint.gaClientId) {
								if (window.lapoint.gaClientId) {
									url += '&clientId=' + window.lapoint.gaClientId
								}

								// Hide all sections except the first (if it has an background image)
								$('.kmc-sections .kmc-section').each(function (index, elem) {
									var $el = $(elem);
									if (index > 0 || !($el.hasClass('has-bgr-img') || $el.hasClass('has-bgr-video'))) {
										$el.fadeOut();
									}
								});

								// Add iframe
								var $iFrame = $('<iframe src="' + url + '" height="800" width="100%" frameborder="0" id="travelize-booking-frame"></iframe>');
								$('#main').append($iFrame);
								$iFrame.width($(window).width());
								$iFrame.hide().fadeIn();
								$iFrame.iFrameResize({
									log: false,
									checkOrigin: false
								});

								$(window).on('resize', function(e) {
									$iFrame.width($(window).width());
								});

								// Close menu book slider if open
								var $main_booking = $(".main-booking");
								if ($main_booking.hasClass("open")) {
									$(".drop-wrapper").parent().removeClass("open");
									$main_booking.removeClass("open");
									$main_booking.slideUp();
								}


							}
						});

						$(this).find(".row").removeClass("row").addClass("row2").each(function (index, row) {
							var $row = $(row);
							var available = $row.find(".colAvailability span").text();
							if ((isNaN(available) && available.substr(0, 1) != "<"  && available.substr(0, 1) != ">") || available == "0") {
								$row.addClass("not-available");
								$row.find(".btn").addClass("disabled");
							}
						});
					}

					$(this).find("span.bookingStatus").remove();

				});
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