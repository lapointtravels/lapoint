define(['jquery', 'underscore', 'backbone', 'TweenLite', 'CSSPlugin'], function($, _, Backbone, TweenLite, CSSPlugin) {

	var PostsSlider = Backbone.View.extend({
		initialize: function () {
			var _this = this;
			var MAX_SLIDE_COUNT = 3;

			var window_width = $(window).width();
			if (window_width < 600) {
				MAX_SLIDE_COUNT = 1;
			} else if (window_width < 1000) {
				MAX_SLIDE_COUNT = 2;
			}

			this.$slider = this.$(".posts-slider");
			this.$prev_slide = this.$(".prev-link");
			this.$next_slide = this.$(".next-link");


			this.TOTAL_SLIDES_COUNT = 0;
			this.slides = [];
			this.slides_left = [];
			this.slides_right = [];

			this.$(".post-slide").each(function (index, elem) {
				_this.TOTAL_SLIDES_COUNT++;
				if (index < MAX_SLIDE_COUNT) {
					_this.slides.push($(elem));
				} else {
					_this.slides_right.push($(elem));
					TweenLite.set($(elem), { x: 1000, y: 0, opacity: 0 });
				}
			});

			var slide_count = Math.min(MAX_SLIDE_COUNT, this.TOTAL_SLIDES_COUNT);
			this.current_slide_count = slide_count;
			this.slide_distance = this.$slider.width() / slide_count;

			// Show initial slides
			this.on_resize();
			setTimeout(function () {
				for (var i=0; i<slide_count; i++) {
					TweenLite.set(_this.slides[i], { x: i * _this.slide_distance, y: 0 });
					TweenLite.to(_this.slides[i], 1, { opacity: 1 });
					_this.slides[i].addClass("visible");
				}
			}, 100);

			this.$prev_slide.on("click", function (e) {
				e.preventDefault();
				_this.slide_right();
			});
			this.$next_slide.on("click", function (e) {
				e.preventDefault();
				_this.slide_left();
			});
			$(".post-slider").on("click", ".post-slide.visible img", function (e) {
				e.preventDefault();
				document.location = $(e.currentTarget).closest(".post-slide").attr("data-href");
			});

			$(window).on("resize", _.bind(this.on_resize, this));
		},

		slide_left: function () {
			if (!this.slides_right.length) return;
			this.animate_out_left(this.slides[0]);
			var pos = 0;
			for (var i=1; i<this.current_slide_count; i++) {
				this.animate_slide(this.slides[i], pos++);
			}

			this.slides_left.push(this.slides.shift());
			this.animate_in_right(this.add_slide_from_right());
		},
		slide_right: function () {
			if (!this.slides_left.length) return;
			this.animate_out_right(this.slides[this.current_slide_count-1]);
			var pos = 1;
			for (var i=0; i<this.current_slide_count-1; i++) {
				this.animate_slide(this.slides[i], pos++);
			}

			this.slides_right.unshift(this.slides.pop());
			this.animate_in_left(this.add_slide_from_left());
		},

		add_slide_from_right: function () {
			if (!this.slides_right.length && !this.slides_left.length) return false;
			var $new_slide = (this.slides_right.length) ? this.slides_right.shift() : this.slides_left.shift();
			this.slides.push($new_slide);
			return $new_slide;
		},
		add_slide_from_left: function () {
			if (!this.slides_right.length && !this.slides_left.length) return false;
			var $new_slide = (this.slides_left.length) ? this.slides_left.pop() : this.slides_right.pop();
			this.slides.unshift($new_slide);
			return $new_slide;
		},
		animate_out_left: function ($slide) {
			TweenLite.to($slide, 1, { x: -100, scale: 0.8 });
			TweenLite.to($slide, 0.8, { opacity: 0 });
			$slide.removeClass("visible");
		},
		animate_out_right: function ($slide) {
			TweenLite.to($slide, 1, { x: (this.current_slide_count-1) * this.slide_distance + 100, scale: 0.8, opacity: 0 });
			TweenLite.to($slide, 0.8, { opacity: 0 });
			$slide.removeClass("visible");
		},
		animate_in_left: function ($slide) {
			if (!$slide) return;
			$slide.addClass("visible");
			TweenLite.set($slide, { x: -100, scale: 0.8, opacity: 0 });
			TweenLite.to($slide, 0.8, { x: 0, scale: 1, opacity: 1 });
		},
		animate_in_right: function ($slide) {
			if (!$slide) return;
			$slide.addClass("visible");
			TweenLite.set($slide, { x: (this.current_slide_count-1) * this.slide_distance + 100, scale: 0.8, opacity: 0 });
			TweenLite.to($slide, 0.8, { x: (this.current_slide_count-1) * this.slide_distance, scale: 1, opacity: 1 });
		},
		animate_slide: function ($slide, index) {
			TweenLite.to($slide, 0.6, { x: index * this.slide_distance });
		},


		on_resize: function () {
			var window_width = $(window).width();
			if (window_width < 600) {
				MAX_SLIDE_COUNT = 1;
			} else if (window_width < 1000) {
				MAX_SLIDE_COUNT = 2;
			} else {
				MAX_SLIDE_COUNT = 3;
			}

			var slide_percent = Math.floor(100 / MAX_SLIDE_COUNT);
			this.$(".post-slide").css("width", slide_percent + "%");

			var slide_count = Math.min(MAX_SLIDE_COUNT, this.TOTAL_SLIDES_COUNT);
			this.slide_distance = this.$slider.width() / slide_count;

			if (this.current_slide_count < slide_count) {
				for (var i=this.current_slide_count; i<slide_count; i++) {
					var $slide = this.add_slide_from_right();
					if ($slide) {
						TweenLite.set($slide, { x: i * this.slide_distance, opacity: 1, scale: 1 });
					}
				}
			} else if (slide_count < this.current_slide_count) {
				var slides_to_remove = this.slides.splice(slide_count, this.current_slide_count-slide_count);
				_.each(slides_to_remove, function ($slide) {
					TweenLite.set($slide, { opacity: 0 });
				});
				for (var  i=slides_to_remove.length-1; i>=0; i--) {
					this.slides_right.unshift( slides_to_remove[i] );
				}
			} else {
				for (var i=0; i<slide_count; i++) {
					TweenLite.set(this.slides[i], { x: i * this.slide_distance });
				}
			}

			this.current_slide_count = slide_count;

			if (slide_count != MAX_SLIDE_COUNT) {
				this.$prev_slide.hide();
				this.$next_slide.hide();
			} else {
				this.$prev_slide.show();
				this.$next_slide.show();
			}

			var max_height = 0;
			this.$(".post-slide").each(function (index, elem) {
				max_height = Math.max($(elem).height(), max_height);
			});
			this.$slider.height(max_height);
		}
	});

	return PostsSlider;

});