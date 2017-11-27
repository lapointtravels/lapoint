(function ($, window, document, undefined) {

	$(function () {

		var ImagesliderComponent = kmc.KmcComponent.extend({
			type: "imageslider",
			label: "Slide show",
			initialize: function (attributes) {
				kmc.KmcComponent.prototype.initialize.apply(this, arguments);

				this.view = new ImagesliderComponentView({
					model: this
				});
			}
		});

		var ImagesliderComponentView = kmc.KmcComponentView.extend({
			template: _.template($("#kmc-imageslider-component-template").html()),
			base_only: true,
			initialize: function () {
				kmc.KmcComponentView.prototype.initialize.apply(this, arguments);

				this.render_base();
			}
		});

		window.KMC_MODULES_MODELS["imageslider"] = ImagesliderComponent;

	});

}(jQuery, window, document));