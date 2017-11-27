(function ($, window, document, undefined) {

	$(function () {

		var QuoteSectionComponent = kmc.KmcComponent.extend({
			type: "quote",
			label: "Quote section",
			extra_defaults: {
				name: "",
				image: ""
			},
			initialize: function (attributes) {
				kmc.KmcComponent.prototype.initialize.apply(this, arguments);

				this.view = new QuoteSectionComponentView({
					model: this
				});
			}
		});

		var QuoteSectionComponentView = kmc.KmcComponentView.extend({
			template: _.template($("#kmc-quote-section-component-template").html()),
			base_only: true,
			auto_set_title: ".post-title",
			initialize: function () {
				kmc.KmcComponentView.prototype.initialize.apply(this, arguments);

				this.render_base();
			},
			extra_events: {
				"click .select-image": "on_select_image_click",
				"click .remove-image": "on_remove_image_click"
			},
			on_select_image_click: function (e) {
				e.preventDefault();
				var _this = this;

				var image = wp.media({
					title: 'Select image',
					button: {
						text: 'Use this image'
					},
					multiple: false
				}).open().on('select', function(e){
					// This will return the selected image from the Media Uploader, the result is an object
					var uploaded_image = image.state().get('selection').first();
					var img_data = uploaded_image.toJSON();
					_this.model.set("image", {
						sizes: img_data.sizes,
						url: img_data.url
					});
					_this.model.set("changed", true);
					_this.render_base();
				});
			},
			on_remove_image_click: function (e) {
				e.preventDefault();
				this.model.set({
					image: false,
					changed: true
				});
				//this.background_image = false;
				//this.render_image_row();
				this.render_base();
			}
		});

		window.KMC_MODULES_MODELS["quote"] = QuoteSectionComponent;

	});

}(jQuery, window, document));