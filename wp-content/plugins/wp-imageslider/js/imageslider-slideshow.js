;(function ($, window, document, undefined) {

	$.detectSwipe.threshold = 30;
	$.detectSwipe.preventDefault = false; // or up and down is active and site doesnt scroll well

	if (!Date.now) {
	    Date.now = function() { return new Date().getTime(); }
	}

	$.fn.imslSlideShow = function(options){
		var $win = $(window),
			slides = [],
			current_id = 0,
			animating = false,
			_this = this,
			$el = $(this),
			current_z = 100,
			current_size,
			TIMER_SEC = parseInt($el.attr('data-timer')),
			itv_timer = null,
			$container = $el.parent(),
			isFullScreen = $el.hasClass('fullscreen');

		
		var win_width = $container.width();

		$('.slide', this).each(function(i){
			var $slide = $(this);
			var slide_width = parseInt($slide.attr('data-width'));
			var slide_height = parseInt($slide.attr('data-height'));
			$slide.slide_width = slide_width;
			$slide.slide_height = slide_height;
			$slide.data_type = $slide.attr('data-type');

			/*
			var width, height;
			if (isFullScreen) {
				width = win_width;
				height = win_height;
			} else {
				width = win_width;
				height = get_height_for_slide($slide);
			}
			*/
			var height = get_height_for_slide($slide);

			$slide
				.hide()
				.width(win_width)
				.height(height);

			if ($slide.data_type == 'youtube') {
				$slide.is_youtube = true;
				$slide.is_vimeo = false;
				$slide.youtube_id = $slide.attr('data-youtube-id');
				$slide.keep_proportions = $slide.attr('data-keep-proportions') == '1';
				$slide.autoplay = $slide.attr('data-autoplay');
				$slide.find('iframe')
					.width(win_width)
					.height(height);
			} else if ($slide.data_type == 'vimeo') {
				$slide.is_vimeo = true;
				$slide.is_youtube = false;
				$slide.video_id = $slide.attr('data-video-id');
				$slide.keep_proportions = $slide.attr('data-keep-proportions') == '1';
				$slide.autoplay = $slide.attr('data-autoplay');
				$slide.find('iframe')
					.width(win_width)
					.height(height);

			} else {
				$slide.is_youtube = false;
				$slide.is_vimeo = false;
				$slide.find('.image')
					.width(win_width)
					.height(height);
			}

			slides.push($slide);
		});

		if (slides.length) {
			//if (isFullScreen) {
			//	$el.height( $win.height() );
			//} else {
				$el.height( get_height_for_slide(slides[0]));
			//}
			setTimeout(function () {
				$el.addClass('ani');
			}, 100);

		}

		function preload_slide ($slide, callback) {
			console.log("preload_slide");
			if (!$slide) return;
			var size = get_size();
			current_size = size;

			if ($slide.is_youtube) {
				$slide.find(".video-container").append(
					'<iframe width="100%" height="640" src="http://www.youtube.com/embed/' + $slide.youtube_id + '?autoplay=' + $slide.autoplay + '&rel=0&showinfo=0&controls=0&autohide=1&color=white" frameborder="0" allowfullscreen></iframe>'
				);
				callback($slide);
			} else if ($slide.is_vimeo) {
				$slide.find(".video-container").append(
					'<iframe width="100%" height="640" src="https://player.vimeo.com/video/' + $slide.video_id + '?title=0&byline=0&portrait=0&badge=0&autoplay=' + $slide.autoplay + '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>'
				);
				callback($slide);
			} else {
				// console.log("PRELOAD", size);
				var src = $slide.attr("data-src-" + size);
				if ($slide.preloaded) {
					callback($slide);
				} else {
					$slide.preloaded = true;
					var img = new Image();
					console.log("Load", img.src);
					img.src = src;
					img.onload = function(){
						$slide.find(".image").css("background-image", "url('" + src + "')");
						console.log("loaded");
						callback($slide);
					}
					img.onerror = function () {
						callback($slide);
					}
				}
			}
		}

		preload_slide(slides[0], function ($slide) {
			$slide.css("z-index", current_z).fadeIn(1000, function () {

				$slide.addClass("open");
				$win.trigger("resize");

				setTimeout(function () {
					_this.addClass("enabled");


					if (!$slide.is_youtube && !$slide.is_vimeo ) {
						reset_timer();
					} else {
						// Make sure vimeo videos are resized properly
						$win.trigger("resize");
						if ($slide.autoplay != "1") {
							reset_timer();
						}
					}
				}, 1000);

			});
		});

		this.update_size = function () {
			// console.log("update_size");
			var win_width = $container.width();
			var size = get_size();

			if (size != current_size) {
				for (var i=0; i<slides.length; i++) {
					slides[i].preloaded = false;
				}
				preload_slide(slides[current_id], function ($slide) {});
				current_size = size;
			}

			var l = slides.length;
			for (var i=0; i<l; i++) {
				var $slide = slides[i],
					height = get_height_for_slide($slide);

				$slide
					.width(win_width)
					.height(height);

				if ($slide.is_youtube || $slide.is_vimeo) {
					$slide.find("iframe")
						.width(win_width)
						.height(height);
				} else {
					$slide.find(".image")
						.width(win_width)
						.height(height);
				}
			}

			//$el.height(height);
			$el.height( get_height_for_slide(slides[current_id]));
		}

		function get_size () {
			var width = $win.width();
			var dpr = window.devicePixelRatio || 1;
			width = width * dpr;

			var sizes = window.KloonImageSliderSizes;
			// var order = ['xs', 'sm', 'md', 'lg', 'xl'];
			var order = window.KloonImageSliderSizesOrder;


			var size = 'lg';
			for (var i=0; i<order.length; i++) {
				// console.log("CHECK", order[i], width, " < ", sizes[order[i]][0]);
				if (sizes[order[i]] && width < sizes[order[i]][0]) {
					// console.log("BREAK");
					size = order[i]

					// Keep remaining sizes for height check below
					order = order.slice(i);
					break;
				}
			}

			if (isFullScreen) {
				var height = $win.height();
				var found = false;
				// Check height as well
				for (var i=0; i<order.length; i++) {
					// console.log("CHECK HEIGT", order[i], height, " < ", sizes[order[i]][1]);
					if (height < sizes[order[i]][1]) {
						// console.log("BREAK");
						size = order[i]
						found = true;
						break;
					}
				}

				if (!found) {
					size = order[order.length - 1];
				}
				/*if (width < size.xs[0]) {
					return "xs";
				} else if (width < 1200) {
					return "md";
				} else {
					return "lg";
				}*/
			}

			return size;
		}

		function get_height_for_slide ($slide) {
			if (($slide.data_type == "youtube" || $slide.data_type == "vimeo") && !$slide.keep_proportions) {
				return $slide.slide_height;
			} else {
				if (isFullScreen) {
					return $win.height();
				} else {
					return Math.max(300, $slide.slide_height * (win_width / $slide.slide_width));
				}
			}
		}

		function stop_slide ($slide) {
			$slide.hide().removeClass("open");
			if ($slide.is_youtube || $slide.is_vimeo) {
				$slide.find(".video-container").empty();
			}
		}

		function show_slide (id) {
			console.log("show_slide", animating);
			if (animating) return;
			animating = true;
			stop_timer();

			$slide = slides[id];

			var delay = 1;
			if ($slide.slide_height < slides[current_id].slide_height) {
				$el.height(get_height_for_slide($slide));
				delay = 300;
			}

			var time = Date.now();
			preload_slide($slide, function ($slide) {
				setTimeout(function () {
					$slide.css("z-index", ++current_z).fadeIn(500, function () {
						stop_slide(slides[current_id]);
						current_id = id;
						console.log("done");
						animating = false;
						$slide.addClass("open");

						if (($slide.is_youtube || $slide.is_vimeo) && $slide.autoplay == "1") {
							// Dont reset timer;
						} else {
							reset_timer();
						}

						$el.height(get_height_for_slide($slide));
						setTimeout(function () {
							$win.trigger("resize");
						}, 250);
					});
				}, Math.max(1, delay - (Date.now() - time)));
			});


			$(".imsl-dots").find("li").removeClass("active");
			$("[data-slide-position='" + id + "']").addClass("active");
		}

		function stop_timer () {
			if (itv_timer) {
				clearInterval(itv_timer);
			}
		}

		function reset_timer () {
			if (itv_timer) {
				clearInterval(itv_timer);
			}
			if (slides.length > 1) {
				itv_timer = setTimeout(show_next_slide, TIMER_SEC * 1000);
			}
		}

		function show_next_slide () {
			var next_id = (current_id < slides.length - 1) ? current_id + 1 : 0;
			show_slide(next_id);
		}

		$(".next-link", this).on("click", function (e) {
			e.preventDefault();
			show_next_slide();
		});
		$(".prev-link", this).on("click", function (e) {
			e.preventDefault();
			var next_id = (current_id > 0) ? current_id - 1 : slides.length - 1;
			show_slide(next_id);
		});

		$(".imsl-dots", this).find("li").css("cursor", "pointer").on("click", function (e) {
			e.preventDefault();
			var $el = $(e.currentTarget),
				position = parseInt($el.attr("data-slide-position"));
			console.log(position);
			show_slide(position);
		})

		$("ul", this).on('swipeleft', function(e){
			e.preventDefault();
			show_next_slide();
		});

		$("ul", this).on('swiperight', function(e){
			e.preventDefault();
			var next_id = (current_id > 0) ? current_id - 1 : slides.length - 1;
			show_slide(next_id);
		});
			

		return this;
	};



	function handle_resize() {
		if (window.imsl_slide_shows) {
			for (var i=0; i<window.imsl_slide_shows.length; i++) {
				window.imsl_slide_shows[i].update_size();
			}
		}
	}


	var itv_resize;
	var on_resize = function () {
		if (itv_resize) {
			clearInterval(itv_resize);
		}
		itv_resize = setTimeout(handle_resize, 200);
	}

	$(document).ready(function($){

		// Make the slide shows global.
		var imsl_slide_shows = [];
		$(".imsl-slide-show").each(function(i){
			var slide_show = $(this).imslSlideShow({});
			imsl_slide_shows.push(slide_show);
		});
		window.imsl_slide_shows = imsl_slide_shows;

		$(window).on("resize", on_resize);
	});

}(jQuery, window, document));