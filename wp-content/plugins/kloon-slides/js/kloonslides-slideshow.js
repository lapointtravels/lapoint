;(function ($, window, document, undefined) {

	$.detectSwipe.threshold = 30;
	$.detectSwipe.preventDefault = false; // or up and down is active and site doesnt scroll well

	var TRANSITION_TIME = 600;

	var VideoModal = Backbone.View.extend({
		className: 'kloonslides-modal-video',
		template: _.template($('#kloonslides-modal-video-template').html()),
		initialize: function (attributes) {
			this.data = attributes;
			this.$el.appendTo($('body'));
			this.$win = $(window);

			this.render();
			this.updateSize();
			this.throttledUpdateSize = _.throttle(_.bind(this.updateSize, this), 100);

			var _this = this;
			setTimeout(function () {
				_this.$el.addClass('active');

				setTimeout(function () {
					_this.$('iframe').css('display', 'block');
				});
			}, 1);

			$(window).on('resize', this.throttledUpdateSize);
			$(document).on('keyup.video_modal', _.bind(this.onKeyUp, this));
		},

		events: {
			'click .close': 'closeModal'
		},

		onKeyUp: function (e) {
			if (e.keyCode === 27) {
				this.closeModal();
			}
		},

		closeModal: function () {
			var _this = this;
			$(window).off('resize', this.throttledUpdateSize);
			$(document).unbind('keyup.video_modal');

			this.$el.find('iframe').remove();
			this.$el.removeClass('active');
			setTimeout(function () {
				_this.$el.remove();
			}, 500);
		},

		updateSize: function () {
			var winWidth = Math.min(this.data.width, this.$win.width() - 80);
			var winHeight = Math.min(this.data.height, this.$win.height() - 100);
			var ratioWidth = winWidth / this.data.width;
			var ratioHeight = winHeight / this.data.height;
			var ratio = Math.min(ratioWidth, ratioHeight);
			var newWidth = Math.round(ratio * this.data.width);
			var newHeight = Math.round(ratio * this.data.height);

			this.$modal
				.css('width',  newWidth + 'px')
				.css('height', newHeight + 'px');
		},

		render: function () {
			this.$el.html(
				this.template(this.data)
			);

			this.$modal = this.$('.video-modal');
		}
	});



	var Slideshow = Backbone.View.extend({
		initialize: function () {
			var _this = this;
			this.$win = $(window);
			this.$container = this.$('.slides-container');
			this.$dots = this.$('.dot');
			this.currentSlide = 0;
			this.itvTimer = null;
			this.timerSec = parseInt(this.$el.attr('data-timer'));
			this.isFixedHeight = this.$el.hasClass('fixed-height');

			if (this.isFixedHeight) {
				this.heights = {
					desktop: this.$el.attr('data-height-desktop'),
					tablet: this.$el.attr('data-height-tablet'),
					phone: this.$el.attr('data-height-phone')
				};

				if (!this.heights.desktop) {
					this.heights.desktop = 800;
				}
				if (!this.heights.tablet) {
					this.heights.tablet = this.heights.desktop;
				}
				if (!this.heights.phone) {
					this.heights.phone = this.heights.tablet;
				}
			}

			if (this.$('.slide').size() > 1) {
				var $firstSlideClone = this.$('.slide:first-child').clone();
				$firstSlideClone.addClass('clone');
				var $lastSlideClone = this.$('.slide:last-child').clone();
				$lastSlideClone.addClass('clone');
				this.$container.append($firstSlideClone);
				this.$container.prepend($lastSlideClone);
			}

			this.slides = new Slides();
			this.$('.slide:not(.clone)').each(function (index, element) {
				if ($(element).attr('data-type') == 'video') {
					var slide = new VideoSlide({
						el: element
					});
				} else {
					var slide = new ImageSlide({
						el: element
					});
				}
				_this.slides.push(slide);
			});

			this.slidesCount = this.slides.length;

			if (this.slidesCount > 1) {
				var firstSlide = this.slides.at(0);
				firstSlide.set('first', true).setupClone(
					this.$('.slide.clone[data-slide-id="' + firstSlide.slideId + '"]')
				);

				var lastSlide = this.slides.at(this.slidesCount - 1);
				lastSlide.set('last', true).setupClone(
					this.$('.slide.clone[data-slide-id="' + lastSlide.slideId + '"]')
				);
			}

			this.currentSize = '';
			this.currentDeviceSize = '';
			this.updateSize();

			if (this.slidesCount) {
				setTimeout(function () {
					_this.$el.addClass('ani');
					_this.$el.addClass('enabled');
				}, 100);

				this.updateDots();
				this.slides.at(0).show();

				if (this.slidesCount > 1) {
					// Set slider position, since there are clones added to the ul
					this.setSliderPosition();
				}

				this.resetTimer();
			}

			var throttledResizeSize = _.throttle(this.onResize.bind(this), 100);
			this.$win.on('resize', throttledResizeSize);

			/*
			this.$container.on('swipeleft', function(e){
				e.preventDefault();
				this.showNextSlide.bind(this);
			});
			this.$container.on('swiperight',  function(e){
				e.preventDefault();
				this.showPrevSlide.bind(this);
			});
			*/
			//this.$container.on('swipeleft', this.showNextSlide.bind(this));
			//this.$container.on('swiperight', this.showPrevSlide.bind(this));
			this.$container.on('swipeleft', this.onNextClick.bind(this));
			this.$container.on('swiperight', this.onPrevClick.bind(this));
		},

		events: {
			'click .next-link': 'onNextClick',
			'click .prev-link': 'onPrevClick',
			'click .dot': 'onDotClick'
		},

		turnTransitionsOn: function () {
			this.$container.addClass('transitions');
		},

		turnTransitionsOff: function () {
			this.$container.removeClass('transitions');
		},

		onNextClick: function (e) {
			e.preventDefault();
			this.showNextSlide(e);
		},

		onPrevClick: function (e) {
			e.preventDefault();
			this.showPrevSlide(e);
		},

		showNextSlide: function () {
			var nextSlide = this.currentSlide + 1;
			if (nextSlide >= this.slidesCount) {
				nextSlide = 0;
			}
			this.showSlide(nextSlide, this.currentSlide + 2);
		},

		showPrevSlide: function (e) {
			e.preventDefault();
			var prevSlide = this.currentSlide - 1;
			if (prevSlide < 0) {
				prevSlide = this.slidesCount - 1;
			}

			this.showSlide(prevSlide, this.currentSlide);
		},

		onDotClick: function (e) {
			var $dot = $(e.currentTarget);
			var position = parseInt($dot.attr('data-slide-position'));
			this.showSlide(position, position + 1);
		},

		showSlide: function (nextSlide, slidePos) {
			if (this.locked) {
				return;
			}
			this.locked = true;

			if (this.currentSlide !== false) {
				var prevSlideObject = this.slides.at(this.currentSlide);
				prevSlideObject.hide();
			}

			this.currentSlide = nextSlide;
			var currentSlideObject = this.slides.at(this.currentSlide);
			currentSlideObject.show();

			this.$container.css('transform', 'translate3d(' + (slidePos * -this.currentWidth) + 'px, 0, 0)');
			this.updateDots();

			var _this = this;
			setTimeout(function () {
				if (currentSlideObject.get('first') || currentSlideObject.get('last')) {
					_this.setSliderPosition();
				}

				_this.resetTimer();
				_this.locked = false;
			}, TRANSITION_TIME)
		},

		stopTimer: function () {
			if (this.itvTimer) {
				clearInterval(this.itvTimer);
			}
		},

		resetTimer: function () {
			this.stopTimer();

			if (this.slidesCount > 1 && this.timerSec) {
				this.itvTimer = setTimeout(this.showNextSlide.bind(this), this.timerSec * 1000);
			}
		},

		updateDots: function () {
			this.$dots
				.removeClass('active')
				.filter('[data-slide-position="' + this.currentSlide + '"]').addClass('active');
		},

		setSliderPosition: function () {
			// var elemIndex = (this.slidesCount > 1) ? (this.currentSlide + 1) : this.currentSlide;
			this.turnTransitionsOff();
			this.$container.css('transform', 'translate3d(' + ((this.currentSlide + 1) * -this.currentWidth) + 'px, 0, 0)');
			this.$container[0].offsetHeight;
			this.turnTransitionsOn();
		},

		onResize: function () {
			this.updateSize();
			this.setSliderPosition();
		},

		updateSize: function () {
			var newSize = this.getSize();
			if (newSize != this.currentSize) {
				this.currentSize = newSize;

				_.each(this.slides.models, function (slide) {
					slide.setSize(newSize);
				});
			}

			this.currentWidth = this.$el.width();
			this.$('.slide').width(this.$el.width());
			this.$('.image').width(this.$el.width());


			if (this.isFixedHeight) {
				var newDeviceSize = this.getDeviceSize();
				if (newDeviceSize != this.currentDeviceSize) {
					this.currentSize = newSize;
					var height = this.heights[newDeviceSize];
					this.$el.height(height);
					this.$container.height(height);
				}
			}
		},

		getSize: function () {
			var width = this.$win.width();
			var dpr = window.devicePixelRatio || 1;
			var settings = window.KloonSlides.settings;
			width = width * dpr;

			var sizes = ['sm', 'md', 'lg'];
			var size = 'lg';

			for (var i=0; i<sizes.length; i++) {
				var mediaLibrarySizeWidth = settings.media_library_sizes[sizes[i]].width;
				if (width < mediaLibrarySizeWidth) {
					size = sizes[i]

					// Keep remaining sizes for height check below
					sizes = sizes.slice(i);
					break;
				}
			}

			return size;
		},

		getDeviceSize: function () {
			var width = this.$win.width();
			if (width < 768) {
				return 'phone';
			} else if (width < 992) {
				return 'tablet';
			} else {
				return 'desktop';
			}
		}
	});


	var AbstractSlide = Backbone.Model.extend({
		initialize: function (attributes) {
			this.cloneView = false;
			this.slideId = this.view.$el.attr('data-slide-id');
		},

		show: function () {
			this.view.$el.addClass('active').addClass('displayed');

			if (this.cloneView) {
				this.cloneView.$el.addClass('active').addClass('displayed');
			}
		},

		hide: function () {
			this.view.$el.removeClass('active');

			if (this.cloneView) {
				this.cloneView.$el.removeClass('active');
			}
		},

		setSize: function (size) {
			this.set('size', size);
		},

		setupClone: function (el) {}
	});


	// ****************************** Video Slide ******************************
	var VideoSlide = AbstractSlide.extend({
		initialize: function (attributes) {
			this.view = new VideoSlideView({
				model: this,
				el: attributes.el
			});

			AbstractSlide.prototype.initialize.apply(this, arguments);
		},

		setupClone: function (el) {
			this.cloneView = new VideoSlideView({
				model: this,
				el: el
			});
		},

		hide: function () {
			AbstractSlide.prototype.hide.apply(this, arguments);
			var _this = this;
			setTimeout(function () {
				_this.view.removeVideo();
			}, TRANSITION_TIME);
		},

		show: function () {
			AbstractSlide.prototype.show.apply(this, arguments);
			this.view.loadVideo();

			if (this.cloneView) {
				this.cloneView.loadVideo();
			}
		}
	});

	var VideoSlideView = Backbone.View.extend({
		initialize: function () {
			this.hasBgrVideo = this.$el.attr('data-has-bgr-video');
			this.youtubeId = this.$el.attr('data-youtube-id');
			this.width = this.$el.attr('data-width');
			this.height = this.$el.attr('data-height');
			this.autoplay = this.$el.attr('data-autoplay');
			this.keepProportions = this.$el.attr('data-keep-proportions');

			if (this.hasBgrVideo) {
				this.video = this.$('video')[0];
			} else {
				this.$container = this.$('.video-container');
			}
			//this.listenTo(this.model, 'change:size', this.updateImageSize.bind(this));
		},

		events: {
			'click .play-video-icon': 'onPlayVideoClick'
		},

		onPlayVideoClick: function (e) {
			new VideoModal({
				url: this.youtubeId,
				width: this.width,
				height: this.height,
				autoplay: this.autoplay
			});
		},

		loadVideo: function () {
			if (this.hasBgrVideo) {
				this.video.play();
			} else {
				this.$container.html(
					'<iframe width="100%" height="640" src="https://www.youtube.com/embed/' + this.youtubeId + '?autoplay=' + this.autoplay + '&rel=0&showinfo=0&controls=0&autohide=1&color=white" frameborder="0" allowfullscreen></iframe>'
				);
			}
		},

		removeVideo: function () {
			if (this.hasBgrVideo) {
				this.video.pause();
			} else {
				this.$container.empty();
			}
		}
	});


	// ****************************** Image Slide ******************************
	var ImageSlide = AbstractSlide.extend({
		initialize: function (attributes) {
			this.view = new ImageSlideView({
				model: this,
				el: attributes.el
			});

			AbstractSlide.prototype.initialize.apply(this, arguments);
		},

		setupClone: function (el) {
			this.cloneView = new ImageSlideView({
				model: this,
				el: el
			});

			this.cloneView.isClone = true;
		},

		setSize: function (size) {
			// Check if the selected image height will cover the entire slide element,
			// otherwise bump the image size if possible.
			var sizes = ['sm', 'md', 'lg'];
			var sizeIndex = sizes.indexOf(size);
			if (this.view.$el.height() > this.view.sizes[size].height && sizeIndex < sizes.length - 1) {
				this.setSize(sizes[sizeIndex + 1]);
			} else {
				AbstractSlide.prototype.setSize.apply(this, [size]);
			}
		}
	});

	var ImageSlideView = Backbone.View.extend({
		initialize: function () {
			this.$image = this.$('.image');
			this.sizes = {
				lg: {
					src:  this.$el.attr('data-lg-src'),
					width: this.$el.attr('data-lg-width'),
					height: this.$el.attr('data-lg-height'),
				},
				md: {
					src:  this.$el.attr('data-md-src'),
					width: this.$el.attr('data-md-width'),
					height: this.$el.attr('data-md-height'),
				},
				sm: {
					src:  this.$el.attr('data-sm-src'),
					width: this.$el.attr('data-sm-width'),
					height: this.$el.attr('data-sm-height'),
				}
			}

			this.listenTo(this.model, 'change:size', this.updateImageSize.bind(this))
		},

		updateImageSize: function () {
			var size = this.model.get('size');
			var sizeObj = this.sizes[size];
			this.$image
				//.width(sizeObj.width)
				//.height(sizeObj.height)
				//.width('100%')
				//.height('auto')
				.css("background-image", "url('" + sizeObj.src + "')");
		}
	});

	var Slides = Backbone.Collection.extend({
		model: AbstractSlide
	});



	$(function ($) {
		$('.kloonslides-slideshow').each(function (index, element) {
			new Slideshow({
				el: element
			});
		});

		//$(window).on("resize", on_resize);
	});


}(jQuery, window, document));
