(function ($, window, document, undefined) {

	$(function () {

		var HeaderComponent = kmc.KmcComponent.extend({
			edit_when_added: false,
			type: "header",
			label: "Generic header",
			initialize: function (attributes) {
				kmc.KmcComponent.prototype.initialize.apply(this, arguments);

				this.view = new HeaderComponentView({
					model: this
				});
			}
		});

		var HeaderComponentView = kmc.KmcComponentView.extend({
			template: _.template($("#kmc-header-component-template").html()),
			base_only: true,
			initialize: function () {
				kmc.KmcComponentView.prototype.initialize.apply(this, arguments);

				this.render_base();
			}
		});

		window.KMC_MODULES_MODELS["header"] = HeaderComponent;

	});

}(jQuery, window, document));