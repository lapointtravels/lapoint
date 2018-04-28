;(function ($, window, document, undefined) {

	$.detectSwipe.threshold = 30;
	$.detectSwipe.preventDefault = false; // or up and down is active and site doesnt scroll well

	if (!Date.now) {
	    Date.now = function() { return new Date().getTime(); }
	}

	$.fn.imslSlideShow = function(options){

		var $win = $(window),
				imslID = options.imslID,
				slides = [],
				current_slide = 1,
				current_slide_id = 0,
				animating = false,
				_this = this,
				$el = $(this),
				current_size,
				TIMER_SEC = parseInt($el.attr('data-timer')),
				CSS_TRANSITION_TIME = 300,
				itv_timer = null,
				$container = $el.parent(),
				$slides_container = $el.find('.slides-container'),
				isFullScreen = $el.hasClass('fullscreen'),
				$firstSlideClone = null,
				$lastSlideClone = null;

		
		var win_width = $container.width();

		// initialize slides
		$('.slide', this).each(function(i){
			var $slide = $(this);
			var slide_width = parseInt($slide.attr('data-width'));
			var slide_height = parseInt($slide.attr('data-height'));
			$slide.slide_width = slide_width;
			$slide.slide_height = slide_height;
			$slide.data_type = $slide.attr('data-type') ? $slide.attr('data-type') : "image";
			$slide.cloneRef = false; 

			var height = get_height_for_slide($slide);

			$slide
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
			$el.height( get_height_for_slide(slides[0]));
			setTimeout(function () {
				$el.addClass('ani');				
			}, 100);
		}

		// if it's not a slide show
		if( slides.length == 1 ) {
			preload_slide( 0, function ($slide) {
				$slide.addClass("open");
			}, false);
			stop_timer();
		// if it is a slideshow
		} else {
			
			$firstSlideClone = setupClone(slides[0]);
			$slides_container.append($firstSlideClone);
			$lastSlideClone = setupClone(slides[slides.length-1]);
			$slides_container.prepend($lastSlideClone);
			// move so we show the first slide and not the appended lastSlideCLone
			$slides_container.css('-webkit-transform', 'translate3d(' + (-win_width) + 'px, 0, 0)');
			animate_slide(1 , false);

			// preload the first slide.
			preload_slide(0, function ($slide) {

				$slide.addClass("open");
				$el.addClass("enabled"); // animates arrows and dot's into view
				transitionsOn();
				reset_timer();
				// preload the last slide
				preload_slide(slides.length-1, null, true);
			}, false);

		}

		function setupClone( $slide ) {

			$clone = $slide.clone();
			$clone.addClass('clone');
			$clone.removeAttr('id');
			$clone.attr('id', $clone.cloneOfId + 'clone')
			$slide.cloneRef = $clone;

			return $clone;
		}

		function transitionsOn() {
			$el.addClass("transitions");
		}
		function transitionsOff() {			
			$el.removeClass("transitions");
		}

		function get_slide_id_from_slide_pos( slide_pos ) {

			if( slide_pos == 0 ) {
				return slides.length - 1;
			} else if( slide_pos > slides.length ) {
				return 0;
			}
			return slide_pos - 1;
		}

		function step_to_slide( delta ) {
			
			if( animating ) {
				return;
			}

			var slide_pos = current_slide+delta;

			var slide_id = get_slide_id_from_slide_pos( slide_pos );

			var cycle = !!( slide_pos == 0 || slide_pos > slides.length );

			if( cycle ) {
				
				show_slide( slide_id, slide_pos, function() {

					transitionsOff();

					current_slide = (slide_pos === 0) ? slides.length : 1;

					animate_slide( current_slide );

					setTimeout(function(){						
						transitionsOn();
					}, 100);

				});

			} else {
				show_slide( slide_id, slide_pos, false );
			}

		}

		function show_slide( slide_id, slide_pos, callback ) {

			if( animating ) {
				return;
			}

			animating = true;

			stop_timer();

			// not exactly sure about the logic or in what case we want to change the height of the slideshow.
			// if it's a video where keep_proportions is false or fullscreen it will return a different value
			if (slides[slide_id].slide_height < slides[current_slide_id].slide_height) {
				$el.height(get_height_for_slide(slides[slide_id]));
			}

			preload_slide( slide_id, function($slide) {

				animate_slide(slide_pos, function() {

					if (($slide.is_youtube || $slide.is_vimeo) && $slide.autoplay == "1") {
							
					} else {
						reset_timer();
					}

					stop_slide( slides[get_slide_id_from_slide_pos(current_slide)] );

					current_slide = slide_pos;
					current_slide_id = get_slide_id_from_slide_pos(slide_pos);

					$slide.addClass("open");

					if( callback ) {
						callback();
					}

					setTimeout(function() {
						animating = false;
					}, 100);

				},false);

				$(".imsl-dots").find("li").removeClass("active");
				$("[data-slide-position='" + slide_id + "']").addClass("active");

			});

		}

		function update_dots( slide_id ) {

			$(".imsl-dots").find("li").removeClass("active");
			$("[data-slide-position='" + slide_id + "']").addClass("active");

		}

		function animate_slide( pos, callback ) {

			//$slides_container.css('-webkit-transform', 'translate3d(' + (pos * -win_width) + 'px, 0, 0)');
			$slides_container.css('-webkit-transform', 'translateX(' + (pos * -win_width) + 'px)');

			if( callback ) {
				setTimeout(function(){
					callback();
				}, CSS_TRANSITION_TIME + 50)
			}

		}
	
		
		// if silent then don't call the callback when preload is done
		function preload_slide (slide_id, callback, silent) {

			if( !slides[slide_id] ) {
				//console.log( "No slide with id: " + slide_id );
				return;
			}

			var $slide = slides[slide_id];
			
			if (!$slide) {
				return;
			}

			if( $slide.preloaded ) {

				// don't pre preload if silent
				if( silent ) {
					//console.log( "slide: " + slide_id + " is already loaded. returning silent");
					return;
				}

				//console.log( "slide: " + slide_id + " is already loaded. calling callback");
				callback($slide);

				// also preload next slide
				if( slide_id < slides.length - 1 ) {
					//console.log( "preload_slide - loading next slide:" + (slide_id + 1));
					preload_slide( slide_id+1, null, true );
				}

				return;
			}

			// preloading videos in silent mode is not possible
			if ( ($slide.is_youtube || $slide.is_vimeo) && silent ) {
				//console.log( "slide: " + slide_id + " is video. returning.");
				// also preload next slide
				if( slide_id < slides.length - 1 ) {
					//console.log( "preload_slide - loading next slide:" + (slide_id + 1));
					preload_slide( slide_id+1, null, true );
				}
				return;
			}

			var size = get_size();
			// current_size is global
			current_size = size;

			if ($slide.is_youtube) {				
				$slide.find(".video-container").append(
					//'<iframe width="100%" height="640" src="http://www.youtube.com/embed/' + $slide.youtube_id + '?autoplay=' + $slide.autoplay + '&rel=0&showinfo=0&controls=0&autohide=1&color=white" frameborder="0" allowfullscreen onload="imsl_slide_shows[' + imslID + '].iFrameLoaded(true,\'' + $slide.youtube_id + '\');"></iframe>'
					'<iframe width="100%" height="640" src="//www.youtube.com/embed/' + $slide.youtube_id + '?autoplay=' + $slide.autoplay + '&rel=0&showinfo=0&controls=0&autohide=1&color=white" frameborder="0" allowfullscreen onload="imsl_slide_shows[' + imslID + '].iFrameLoaded();"></iframe>'
				);
				callback($slide);
			} else if ($slide.is_vimeo) {
				$slide.find(".video-container").append(
					//'<iframe width="100%" height="640" src="https://player.vimeo.com/video/' + $slide.video_id + '?title=0&byline=0&portrait=0&badge=0&autoplay=' + $slide.autoplay + '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen onload="imsl_slide_shows[' + imslID + '].iFrameLoaded(false,\'' + $slide.video_id + '\');"></iframe>'
					'<iframe width="100%" height="640" src="//player.vimeo.com/video/' + $slide.video_id + '?title=0&byline=0&portrait=0&badge=0&autoplay=' + $slide.autoplay + '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen onload="imsl_slide_shows[' + imslID + '].iFrameLoaded();"></iframe>'
				);
				callback($slide);
			} else {

				//console.log("preloading slide: " + slide_id + " with size: ", size);

				var src = $slide.attr("data-src-" + size);
				if ($slide.preloaded) {
					if (silent) {
						return;
					}
					callback($slide);
				} else {
					$slide.preloaded = true;
					var img = new Image();
					img.src = src;
					img.onload = function(){
						console.log("slide: " + slide_id + " loaded with size: ", size);
						$slide.find(".image").css("background-image", "url('" + src + "')");
						// add to the clone if it has one
						if( $slide.cloneRef ) {
							$slide.cloneRef.find(".image").css("background-image", "url('" + src + "')");
						}
						if( silent ) {
							//console.log( "preload_slide returning silent for slide: " + slide_id );
							return;
						}
						callback($slide);

						// also preload next slide
						if( slide_id < slides.length - 1 ) {
							//console.log( "preload_slide - loading next slide:" + (slide_id + 1));
							preload_slide( slide_id+1, null, true );
						}
					}
					img.onerror = function () {
						if(silent) {
							return;
						}
						callback($slide);
					}
				}
			}
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

		function stop_slide ($slide) {
			$slide.removeClass("open");
			if ($slide.is_youtube || $slide.is_vimeo) {
				$slide.find(".video-container").empty();
			}
		}

		function show_next_slide() {
			step_to_slide( 1 );
		}

		function show_previous_slide() {
			step_to_slide( -1 );
		}

		$(".next-link", this).on("click", function (e) {
			e.preventDefault();
			show_next_slide();
		});
		$(".prev-link", this).on("click", function (e) {
			e.preventDefault();
			show_previous_slide();
		});

		$(".imsl-dots", this).find("li").css("cursor", "pointer").on("click", function (e) {
			e.preventDefault();
			var $el = $(e.currentTarget),
				position = parseInt($el.attr("data-slide-position"));
			show_slide(position, position+1, false);
		})

		$el.on('swipeleft', function(e){
			e.preventDefault();
			show_next_slide();
		});

		$el.on('swiperight', function(e){
			e.preventDefault();
			show_previous_slide();
		});

		this.update_size = function () {

			var new_width = $container.width();
			var tweakSliderPos = win_width != new_width ? true : false;
			win_width = new_width;

			var size = get_size();

			if (size != current_size) {

				// don't downsize 
				if( window.KloonImageSliderSizes[size][0] > window.KloonImageSliderSizes[current_size][0] ) {
					for (var i=0; i<slides.length; i++) {
						slides[i].preloaded = false;
					}
					preload_slide(current_slide_id, null, true);
				}

				current_size = size;
			}

			var l = slides.length;
			for (var i=0; i<l; i++) {
				var $slide = slides[i],
					height = get_height_for_slide($slide);

				$slide
					.width(win_width)
					.height(height);

				if( $slide.cloneRef ) {
					$slide.cloneRef
						.width(win_width)
						.height(height);
					}

				if ($slide.is_youtube || $slide.is_vimeo) {
					$slide.find("iframe")
						.width(win_width)
						.height(height);		
					if( $slide.cloneRef ) {			
						$slide.cloneRef.find("iframe")
							.width(win_width)
							.height(height);
					}

				} else {
					$slide.find(".image")
						.width(win_width)
						.height(height);
					if( $slide.cloneRef ) {
						$slide.cloneRef.find(".image")
							.width(win_width)
							.height(height);
					}
				}
			}

			//$el.height(height);
			$el.height( get_height_for_slide(slides[current_slide_id]));

			if( tweakSliderPos ) {
				stop_timer();
				transitionsOff();
				animate_slide( current_slide, false );
				setTimeout(transitionsOn, 10);
				if (($slide.is_youtube || $slide.is_vimeo) && $slide.autoplay == "1") {
					
				} else {
					reset_timer();
				}
			}

		}

		this.iFrameLoaded = function () {
			$win.trigger("resize");
		}
			

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
		itv_resize = setTimeout(handle_resize, 500);
	}

	$(document).ready(function($){

		// Make the slide shows global.
		var imsl_slide_shows = [];
		$(".imsl-slide-show").each(function(i){
			var slide_show = $(this).imslSlideShow({imslID:i});
			imsl_slide_shows.push(slide_show);
		});
		window.imsl_slide_shows = imsl_slide_shows;

		$(window).on("resize", on_resize);
	});

}(jQuery, window, document));
