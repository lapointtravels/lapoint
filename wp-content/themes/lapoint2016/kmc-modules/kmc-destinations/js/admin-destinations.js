(function ($, window, document, undefined) {

	$(function () {

		var DestinationsComponent = kmc.KmcComponent.extend({
			edit_when_added: false,
			type: "destination_boxes",
			label: "Destination Boxes",

			extra_defaults: {
				tag: 'h2'
			},

			initialize: function (attributes) {
				kmc.KmcComponent.prototype.initialize.apply(this, arguments);

				this.view = new DestinationsComponentView({
					model: this
				});
			}
		});

		var DestinationsComponentView = kmc.KmcComponentView.extend({
			template: _.template($("#kmc-destinations-component-template").html()),
			base_only: true,
			auto_set_title: ".post-title",
			auto_set_content: ".post-content",
			initialize: function () {
				kmc.KmcComponentView.prototype.initialize.apply(this, arguments);

				this.render_base();
			}
		});

		window.KMC_MODULES_MODELS["destination_boxes"] = DestinationsComponent;

	});

}(jQuery, window, document));