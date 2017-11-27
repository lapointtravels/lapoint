define(['jquery', 'underscore', 'backbone', 'TweenLite', 'CSSPlugin'], function($, _, Backbone, TweenLite, CSSPlugin) {

	var VideoModal = Backbone.View.extend({
		className: 'modal-video-container',
		template: _.template($('#modal-video-template').html()),
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

	var VideoSlider = Backbone.View.extend({
		initialize: function () {
			var _this = this;

			this.$win = $(window);
			this.$container = this.$('.videos-slider');
			this.$wrapper = this.$(".wrapper");
			this.$prevSlide = this.$(".prev-link");
			this.$nextSlide = this.$(".next-link");

			this.position = 0;
			this.navActive = false;

			$(window).on("resize", _.throttle(_.bind(this.onResize, this), 100));
			this.onResize();

			this.$container.find('.video-slide').each(function (i, elem) {
				$(elem).find('img').css('transition-delay', (i * 200) + 'ms');
			});
			this.$container.addClass('active');
		},

		events: {
			'click .prev-link': 'onPrevClick',
			'click .next-link': 'onNextClick',
			'click [data-video-url]': 'onVideoClick'
		},

		onVideoClick: function (e) {
			var $el = $(e.currentTarget);
			new VideoModal({
				url: $el.attr('data-video-url'),
				width: $el.attr('data-width'),
				height: $el.attr('data-height'),
				autoplay: $el.attr('data-autoplay')
			});
		},

		onPrevClick: function () {
			this.position += 300;
			if (this.position > 0) {
				this.position = 0;
			}
			this.$wrapper.css('transform', 'translateX(' + this.position + 'px)');
		},

		onNextClick: function () {
			this.position -= 300;
			this.checkSideLimit();
			this.$wrapper.css('transform', 'translateX(' + this.position + 'px)');
		},

		checkSideLimit: function () {
			var winWidth = this.$win.width();
			var wrapperWidth = this.$wrapper.width();
			var limit = winWidth - wrapperWidth;
			if (this.position < limit) {
				this.position = limit;
				return true;
			}
			return false;
		},

		onResize: function () {
			var winWidth = this.$win.width();
			var wrapperWidth = this.$wrapper.width();

			this.navActive = (wrapperWidth > winWidth);
			if (this.navActive) {
				this.$container.addClass('nav-active');
				if (this.checkSideLimit()) {
					this.$wrapper.css('transform', 'translateX(' + this.position + 'px)');
				}
			} else {
				this.$container.removeClass('nav-active');
				this.position = 0;
				this.$wrapper.css('transform', 'translateX(' + this.position + 'px)');
			}
		}
	});

	return VideoSlider;

});