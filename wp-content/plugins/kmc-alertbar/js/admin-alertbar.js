(function ($, window, document, undefined) {

	$(function () {

		var AlertbarComponent = kmc.KmcComponent.extend({
			type: "alertbar",
			label: "Alertbar",
			extra_defaults: {
				button_text: "",
				button_link: ""
			},
			initialize: function (attributes) {
				kmc.KmcComponent.prototype.initialize.apply(this, arguments);

				this.view = new AlertbarComponentView({
					model: this
				});
			}
		});

		var AlertbarComponentView = kmc.KmcComponentView.extend({
			template: _.template($("#kmc-alertbar-component-template").html()),
			base_only: true,
			auto_set_title: ".post-title",
			initialize: function () {
				kmc.KmcComponentView.prototype.initialize.apply(this, arguments);

				this.render_base();
			}
		});

		window.KMC_MODULES_MODELS["alertbar"] = AlertbarComponent;

	});

}(jQuery, window, document));