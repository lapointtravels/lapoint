(function ($, window, document, undefined) {

	$(function () {

		var ImageSectionComponent = kmc.KmcComponent.extend({
			type: "image-section",
			label: "Image section",
			extra_defaults: {
				image: ""
			},
			initialize: function (attributes) {
				kmc.KmcComponent.prototype.initialize.apply(this, arguments);

				this.view = new ImageSectionComponentView({
					model: this
				});
			}
		});

		var ImageSectionComponentView = kmc.KmcComponentView.extend({
			template: _.template($("#kmc-image-section-component-template").html()),
			base_only: true,
			auto_set_title: ".post-title",
			auto_set_content: ".post-content",
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
					/*var img = {};
					img.thumbnail = img_data["sizes"]["thumbnail"].url;
					img.sizes = {};
					img.sizes["box-md"] = img_data["sizes"]["box-md"];
					img.sizes["box-sm"] = img_data["sizes"]["box-sm"];

					_this.background_image = img;*/
					console.log("Image data", img_data);
					_this.model.set("image", {
						sizes: img_data.sizes,
						url: img_data.url
					});
					_this.model.set("changed", true);
					console.log("Attributes: ", _this.model.attributes);
					//_this.render_image_row();
					_this.render_base();
				});
			},
			on_remove_image_click: function (e) {
				e.preventDefault();
				this.background_image = false;
				this.render_image_row();
			}
		});

		window.KMC_MODULES_MODELS["image-section"] = ImageSectionComponent;

	});

}(jQuery, window, document));