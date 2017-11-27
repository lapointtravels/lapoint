(function ($, window, document, undefined) {

	$(function () {

		var PackagesComponent = kmc.KmcComponent.extend({
			edit_when_added: false,
			type: "packages",
			label: "Package Boxes",
			initialize: function (attributes) {
				kmc.KmcComponent.prototype.initialize.apply(this, arguments);

				this.view = new PackagesComponentView({
					model: this
				});
			}
		});

		var PackagesComponentView = kmc.KmcComponentView.extend({
			template: _.template($("#kmc-packages-component-template").html()),
			base_only: true,
			auto_set_title: ".post-title",
			auto_set_content: ".post-content",
			initialize: function () {
				kmc.KmcComponentView.prototype.initialize.apply(this, arguments);

				this.render_base();
			}
		});

		window.KMC_MODULES_MODELS["packages"] = PackagesComponent;

	});

}(jQuery, window, document));