(function ($, window, document, undefined) {

	$(function () {

		var SlideshowComponent = kmc.KmcComponent.extend({
			type: 'kmcslides',
			label: 'Slideshow',
			extra_defaults: {
				slideshow_id: false
			},

			initialize: function (attributes) {
				kmc.KmcComponent.prototype.initialize.apply(this, arguments);

				var _this = this;
				this.set('slideshow', false);
				this.set('error', false);
				this._lastSlideshowId = this.get('slideshow_id');

				this.view = new SlideshowComponentView({
					model: this
				});

				this.listenTo(this, 'change:edit', function () {
					if (_this._lastSlideshowId !== _this.get('slideshow_id')) {
						_this._lastSlideshowId = _this.get('slideshow_id');
						_this.updateSlideshowSlides();
					}
				});

				this.updateSlideshowSlides();
			},

			updateSlideshowSlides: function () {
				var _this = this;

				if (this.get('slideshow_id')) {
					this.set('error', false);
					this.set('slideshow', false);
					this.view.render_base();

					$.get(ajaxurl, {
						action: 'kloonslides_get_slideshow_json',
						slideshow_id: this.get('slideshow_id')
					}, function (response) {
						if (response.status == 200 && response.data) {
							_this.set('slideshow', response.data.slideshow);
							_this.view.render_base();
						} else {
							_this.set('error', 'Preview could not be loaded');
							_this.view.render_base();
						}
					});
				} else {
					this.set('error', 'No slideshow selected');
					this.set('slideshow', false);
					this.view.render_base();
				}
			}
		});

		var SlideshowComponentView = kmc.KmcComponentView.extend({
			template: _.template($('#kmc-kmcslides-component-template').html()),
			base_only: true,

			initialize: function () {
				kmc.KmcComponentView.prototype.initialize.apply(this, arguments);

				this.render_base();
			}
		});

		window.KMC_MODULES_MODELS['kmcslides'] = SlideshowComponent;

	});

}(jQuery, window, document));
